<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GameResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'external_id' => $this->external_id,
            'competition_code' => $this->competition_code,
            'home_team_id' => $this->home_team_id,
            'away_team_id' => $this->away_team_id,
            'score_home' => $this->score_home,
            'score_away' => $this->score_away,
            'status' => $this->status,
            'utc_date' => $this->utc_date?->toIso8601String(),
            'minute' => $this->minute,
            'home_team' => new TeamResource($this->whenLoaded('homeTeam')),
            'away_team' => new TeamResource($this->whenLoaded('awayTeam')),
        ];
    }
}