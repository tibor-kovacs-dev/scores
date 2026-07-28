<?php

namespace App\Events;

use App\Models\Game;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MatchUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $game;

    public function __construct(Game $game)
    {
        $this->game = $game->load(['homeTeam', 'awayTeam']);
    }

    public function broadcastOn(): array
    {
        return [
            new Channel('matches'),
        ];
    }

    public function broadcastAs(): string
    {
        return 'match.updated';
    }

    public function broadcastWith(): array
    {
        $this->game->loadMissing(['homeTeam', 'awayTeam']);

        return [
            'game' => [
                'id' => $this->game->id,
                'external_id' => $this->game->external_id,
                'competition_code' => $this->game->competition_code,
                'home_team_id' => $this->game->home_team_id,
                'away_team_id' => $this->game->away_team_id,
                'score_home' => $this->game->score_home,
                'score_away' => $this->game->score_away,
                'status' => $this->game->status,
                'utc_date' => $this->game->utc_date?->toIso8601String(),
                'minute' => $this->game->minute,
                'home_team' => $this->game->homeTeam ? [
                    'id' => $this->game->homeTeam->id,
                    'name' => $this->game->homeTeam->name,
                    'short_name' => $this->game->homeTeam->short_name,
                    'logo_url' => $this->game->homeTeam->logo_url,
                ] : null,
                'away_team' => $this->game->awayTeam ? [
                    'id' => $this->game->awayTeam->id,
                    'name' => $this->game->awayTeam->name,
                    'short_name' => $this->game->awayTeam->short_name,
                    'logo_url' => $this->game->awayTeam->logo_url,
                ] : null,
            ],
        ];
    }
}
