<?php

namespace App\Http\Controllers;

use App\Models\Game;
use App\Models\Team;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function search(Request $request)
    {
        $query = trim($request->get('q', ''));

        if (strlen($query) < 2) {
            return response()->json(['teams' => [], 'games' => []]);
        }

        $competition = $request->query('competition');

        $teams = Team::where(function ($q) use ($query) {
            $q->where('name', 'LIKE', "%{$query}%")
                ->orWhere('short_name', 'LIKE', "%{$query}%")
                ->orWhere('tla', 'LIKE', "%{$query}%");
        })
            ->select('id', 'external_id', 'name', 'short_name', 'logo_url', 'tla')
            ->limit(8)
            ->get();

        $gamesQuery = Game::with([
            'homeTeam:id,name,short_name,logo_url',
            'awayTeam:id,name,short_name,logo_url',
        ])
            ->where(function ($q) use ($query) {
                $q->whereHas('homeTeam', fn ($q) => $q->where('name', 'LIKE', "%{$query}%")
                    ->orWhere('short_name', 'LIKE', "%{$query}%"))
                    ->orWhereHas('awayTeam', fn ($q) => $q->where('name', 'LIKE', "%{$query}%")
                        ->orWhere('short_name', 'LIKE', "%{$query}%"));
            })
            ->orderBy('utc_date');

        if ($competition && $competition !== 'ALL') {
            $gamesQuery->where('competition_code', $competition);
        }

        $games = $gamesQuery->limit(10)->get();

        return response()->json([
            'teams' => $teams,
            'games' => $games,
        ]);
    }
}
