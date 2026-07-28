<?php

namespace App\Http\Controllers;

use App\Http\Resources\GameResource;
use App\Models\Game;
use Carbon\Carbon;
use Illuminate\Http\Request;

class GameController extends Controller
{
    public function index(Request $request)
    {
        $query = Game::with(['homeTeam', 'awayTeam'])
            ->orderBy('utc_date', 'desc');   // legfrissebbek előre

        if ($request->date) {
            $date = Carbon::parse($request->date);
            $query->whereBetween('utc_date', [
                $date->copy()->subDay()->startOfDay(),
                $date->copy()->endOfDay()
            ]);
        } else {
            $query->whereBetween('utc_date', [
                now()->subDay()->startOfDay(),
                now()->addDay()->endOfDay()
            ]);
        }

        if ($request->competition && $request->competition !== 'ALL') {
            $query->where('competition_code', $request->competition);
        }

        if ($request->boolean('live')) {
            $query->whereIn('status', ['LIVE', 'IN_PLAY', 'PAUSED', 'FIRST_HALF', 'SECOND_HALF', 'HALF_TIME']);
        }

        $games = $query->paginate(50);

        return GameResource::collection($games);
    }

    public function show($id)
    {
        $game = Game::with(['homeTeam', 'awayTeam'])->find($id);

        if (!$game) {
            return response()->json(['message' => 'Match not found'], 404);
        }

        return new GameResource($game);
    }
}