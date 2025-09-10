<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Team;

class TeamController extends Controller
{
    public function index()
    {
        try {
            $teams = Team::with('funktion')
                ->orderBy('Bezeichnung')
                ->get()
                ->map(function($team) {
                    return [
                        'id' => $team->TeamID,
                        'name' => $team->Bezeichnung,
                        'funktion' => $team->funktion ? $team->funktion->Bezeichnung : null
                    ];
                });

            // If no data found, return fallback message
            if ($teams->isEmpty()) {
                return response()->json([['name' => 'Keine Daten gefunden']]);
            }

            return response()->json($teams);
        } catch (\Exception $e) {
            return response()->json([['name' => 'Keine Daten gefunden']]);
        }
    }
}