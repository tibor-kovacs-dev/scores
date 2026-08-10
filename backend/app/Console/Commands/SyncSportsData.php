<?php

namespace App\Console\Commands;

use App\Services\FootballDataService;
use Illuminate\Console\Command;

class SyncSportsData extends Command
{
    protected $signature = 'sports:sync {--live-only : Only sync live matches}';

    protected $description = 'Sync teams and matches from all leagues';

    public function handle(FootballDataService $service)
    {
        if ($this->option('live-only')) {
            $this->info('🔴 Updating live matches only...');
            $service->syncLiveMatches();

            return;
        }

        $this->info('Starting full sync for all leagues...');
        $start = microtime(true);

        $this->info('Fetching teams from all leagues...');
        $service->syncTeams();

        $teamCount = \App\Models\Team::count();
        $this->info("{$teamCount} teams loaded");

        $this->info('Syncing matches from all leagues...');
        $service->syncMatches();

        $gameCount = \App\Models\Game::count();
        $this->info("{$gameCount} matches in database");

        // Live matches are intentionally NOT re-synced here anymore.
        // A separate `sports:sync --live-only` job already covers that
        // every minute; calling syncLiveMatches() here too was redundant
        // and was part of what exhausted the football-data.org rate limit.

        $this->info('Cleaning up old matches...');
        $deleted = $service->cleanupOldMatches(30);
        $this->info("{$deleted} old matches deleted");

        // Team/competition caches are no longer force-cleared on every
        // run. They already expire on their own TTL (6h / 12h); clearing
        // them every 5 minutes defeated that TTL and forced needless
        // API refetches.

        $duration = round(microtime(true) - $start, 2);
        $this->info("Sync completed! ({$duration} seconds)");
    }
}