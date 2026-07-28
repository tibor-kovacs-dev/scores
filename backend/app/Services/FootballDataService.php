<?php

namespace App\Services;

use App\Events\MatchUpdated;
use App\Models\Game;
use App\Models\Team;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FootballDataService
{
    protected $apiKey;

    protected $baseUrl = 'https://api.football-data.org/v4/';

    public function __construct()
    {
        $this->apiKey = config('services.football_data.key');
    }

    private function getAvailableCompetitions(): array
    {
        return cache()->remember('available_competitions', now()->addHours(12), function () {
            try {
                $response = Http::withHeaders(['X-Auth-Token' => $this->apiKey])
                    ->timeout(10)
                    ->get($this->baseUrl.'competitions');

                $comps = $response->json()['competitions'] ?? [];

                return collect($comps)
                    ->pluck('code')
                    ->filter()
                    ->unique()
                    ->values()
                    ->toArray();
            } catch (\Exception $e) {
                Log::error('Failed to fetch competitions: '.$e->getMessage());

                return ['WC', 'PL', 'PD', 'SA', 'BL1', 'BL2', 'ELC', 'FL1', 'BSA', 'CLI'];
            }
        });
    }

    public function syncTeams($leagueCode = null)
    {
        $leagueCodes = $leagueCode ? [$leagueCode] : $this->getAvailableCompetitions();

        foreach ($leagueCodes as $code) {
            $data = cache()->remember("teams_{$code}", now()->addHours(6), function () use ($code) {
                try {
                    $response = Http::withHeaders(['X-Auth-Token' => $this->apiKey])
                        ->timeout(10)
                        ->get($this->baseUrl."competitions/{$code}/teams");

                    return $response->json()['teams'] ?? [];
                } catch (\Exception $e) {
                    Log::error("Teams sync failed for {$code}: ".$e->getMessage());

                    return [];
                }
            });

            foreach ($data as $teamData) {
                Team::updateOrCreate(
                    ['external_id' => $teamData['id']],
                    [
                        'name' => $teamData['name'],
                        'short_name' => $teamData['shortName'] ?? $teamData['name'],
                        'tla' => $teamData['tla'] ?? '',
                        'logo_url' => $teamData['crest'] ?? null,
                    ]
                );
            }

            sleep(2);
        }
    }

    public function syncMatches($leagueCode = null)
    {
        $this->syncMatchesByDateRange(
            now()->subDays(2)->toDateString(),
            now()->addDays(5)->toDateString()
        );
    }

    private function syncMatchesByDateRange(string $from, string $to)
    {
        Log::info("Syncing matches by date range: {$from} → {$to}");

        try {
            $response = Http::withHeaders(['X-Auth-Token' => $this->apiKey])
                ->timeout(20)
                ->get($this->baseUrl.'matches', [
                    'dateFrom' => $from,
                    'dateTo' => $to,
                ]);

            if ($response->failed()) {
                Log::warning('Date range fetch failed: '.$response->status());

                return;
            }

            $matches = $response->json()['matches'] ?? [];
            Log::info('Downloaded: '.count($matches).' matches');

            $this->processMatches($matches);
        } catch (\Exception $e) {
            Log::error('Date range sync failed: '.$e->getMessage());
        }
    }

    public function syncLiveMatches()
    {
        try {
            $response = Http::withHeaders(['X-Auth-Token' => $this->apiKey])
                ->timeout(10)
                ->get($this->baseUrl.'matches?status=LIVE,IN_PLAY,PAUSED');

            if ($response->failed()) {
                Log::warning('Live fetch failed: '.$response->status());

                return;
            }

            $matches = $response->json()['matches'] ?? [];
            Log::info('Live/Paused matches: '.count($matches));

            $this->processMatches($matches, true);
        } catch (\Exception $e) {
            Log::error('Live sync error: '.$e->getMessage());
        }
    }

    private function processMatches(array $matches, bool $isLive = false, ?string $competitionCode = null)
    {
        $teamIds = collect($matches)->flatMap(fn ($m) => [
            $m['homeTeam']['id'] ?? null,
            $m['awayTeam']['id'] ?? null,
        ])->filter()->unique();

        $existingTeams = Team::whereIn('external_id', $teamIds)->get()->keyBy('external_id');

        foreach ($matches as $m) {
            $homeData = $m['homeTeam'] ?? null;
            $awayData = $m['awayTeam'] ?? null;

            if (! $homeData || ! $awayData) {
                continue;
            }

            $homeTeam = $existingTeams[$homeData['id']] ?? Team::updateOrCreate(
                ['external_id' => $homeData['id']],
                [
                    'name' => $homeData['name'],
                    'short_name' => $homeData['shortName'] ?? $homeData['name'],
                    'tla' => $homeData['tla'] ?? '',
                    'logo_url' => $homeData['crest'] ?? null,
                ]
            );

            $awayTeam = $existingTeams[$awayData['id']] ?? Team::updateOrCreate(
                ['external_id' => $awayData['id']],
                [
                    'name' => $awayData['name'],
                    'short_name' => $awayData['shortName'] ?? $awayData['name'],
                    'tla' => $awayData['tla'] ?? '',
                    'logo_url' => $awayData['crest'] ?? null,
                ]
            );

            $compCode = $competitionCode
                ?? ($m['competition']['code'] ?? null)
                ?? 'UNKNOWN';

            $oldGame = Game::where('external_id', $m['id'])->first();

            $game = Game::updateOrCreate(
                ['external_id' => $m['id']],
                [
                    'competition_code' => $compCode,
                    'home_team_id' => $homeTeam->id,
                    'away_team_id' => $awayTeam->id,
                    'score_home' => $this->getHomeScore($m),
                    'score_away' => $this->getAwayScore($m),
                    'status' => $m['status'],
                    'utc_date' => Carbon::parse($m['utcDate']),
                    'minute' => $this->getCurrentMinute($m) ?? 0,
                ]
            );

            $changed = ! $oldGame
                || $oldGame->score_home != $game->score_home
                || $oldGame->score_away != $game->score_away
                || $oldGame->status !== $game->status
                || $oldGame->minute != $game->minute;

            if ($changed) {
                event(new MatchUpdated($game));
            }
        }
    }

    private function getHomeScore(array $match): int
    {
        $score = $match['score'] ?? [];

        return $score['fullTime']['home']
            ?? $score['regularTime']['home']
            ?? $score['halfTime']['home']
            ?? 0;
    }

    private function getAwayScore(array $match): int
    {
        $score = $match['score'] ?? [];

        return $score['fullTime']['away']
            ?? $score['regularTime']['away']
            ?? $score['halfTime']['away']
            ?? 0;
    }

    private function getCurrentMinute(array $match): ?int
    {
        if (isset($match['minute']) && is_numeric($match['minute'])) {
            return (int) $match['minute'];
        }
        if (isset($match['score']['currentMinute']) && is_numeric($match['score']['currentMinute'])) {
            return (int) $match['score']['currentMinute'];
        }

        return null;
    }

    public function getStandings($leagueCode = 'PL')
    {
        $cacheKey = "standings_{$leagueCode}_".date('Y');

        return cache()->remember($cacheKey, now()->addMinutes(15), function () use ($leagueCode) {
            try {
                $response = Http::withHeaders(['X-Auth-Token' => $this->apiKey])
                    ->timeout(10)
                    ->get("{$this->baseUrl}competitions/{$leagueCode}/standings");

                return $response->successful() ? $response->json() : null;
            } catch (\Exception $e) {
                Log::error("Standings fetch failed for {$leagueCode}: ".$e->getMessage());

                return null;
            }
        });
    }

    public function getTopScorers($leagueCode = 'PL')
    {
        $cacheKey = "top_scorers_{$leagueCode}_".date('Y');

        return cache()->remember($cacheKey, now()->addMinutes(20), function () use ($leagueCode) {
            try {
                $response = Http::withHeaders(['X-Auth-Token' => $this->apiKey])
                    ->timeout(10)
                    ->get("{$this->baseUrl}competitions/{$leagueCode}/scorers", [
                        'season' => date('Y'),
                        'limit' => 20,
                    ]);

                return $response->successful() ? $response->json() : null;
            } catch (\Exception $e) {
                Log::warning("Top scorers fetch failed for {$leagueCode}: ".$e->getMessage());

                return null;
            }
        });
    }

    public function getKnockoutStage($leagueCode = 'WC')
    {
        $cacheKey = "knockout_{$leagueCode}_".date('Y');

        return cache()->remember($cacheKey, now()->addMinutes(10), function () use ($leagueCode) {
            try {
                $response = Http::withHeaders(['X-Auth-Token' => $this->apiKey])
                    ->timeout(10)
                    ->get("{$this->baseUrl}competitions/{$leagueCode}/matches", [
                        'season' => date('Y'),
                        'stage' => 'LAST_16,QUARTER_FINALS,SEMI_FINALS,THIRD_PLACE,FINAL',
                    ]);

                return $response->successful() ? $response->json() : null;
            } catch (\Exception $e) {
                Log::warning("Knockout stage fetch failed for {$leagueCode}: ".$e->getMessage());

                return null;
            }
        });
    }

    public function cleanupOldMatches(int $days = 30): int
    {
        $cutoff = now()->subDays($days)->startOfDay();

        $deleted = Game::where('utc_date', '<', $cutoff)
            ->whereNotIn('status', ['IN_PLAY', 'LIVE', 'PAUSED', 'FIRST_HALF', 'SECOND_HALF', 'HALF_TIME'])
            ->delete();

        Log::info("Cleanup: deleted {$deleted} matches older than {$days} days");

        return $deleted;
    }

    public function clearCaches(): void
    {
        cache()->forget('available_competitions');

        $codes = $this->getAvailableCompetitions();

        foreach ($codes as $code) {
            cache()->forget("teams_{$code}");
            cache()->forget("standings_{$code}_".date('Y'));
            cache()->forget("top_scorers_{$code}_".date('Y'));
        }

        Log::info('Caches cleared for competitions: '.implode(', ', $codes));
    }
}