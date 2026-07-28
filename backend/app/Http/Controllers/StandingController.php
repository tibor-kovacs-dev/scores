<?php

namespace App\Http\Controllers;

use App\Services\FootballDataService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StandingController extends Controller
{
    protected FootballDataService $service;

    public function __construct(FootballDataService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request): JsonResponse
    {
        $code = $request->query('competition', 'PL');
        $standings = $this->service->getStandings($code);

        if (!$standings) {
            return response()->json(['error' => 'Nem sikerült betölteni a tabellát'], 500);
        }

        return response()->json($standings);
    }

    public function topScorers(Request $request): JsonResponse
    {
        $code = $request->query('competition', 'PL');
        $scorers = $this->service->getTopScorers($code);

        if (!$scorers) {
            return response()->json(['error' => 'Nem sikerült betölteni a góllövőket'], 500);
        }

        return response()->json($scorers);
    }
}