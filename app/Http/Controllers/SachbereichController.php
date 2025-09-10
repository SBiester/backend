<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Team;

class SachbereichController extends Controller
{
    public function index()
    {
        try {
            $sachbereiche = Team::with('funktion')
                ->orderBy('Bezeichnung')
                ->get()
                ->map(function($team) {
                    return [
                        'value' => $team->Bezeichnung,
                        'label' => $team->Bezeichnung,
                        'id' => $team->TeamID,
                        'name' => $team->Bezeichnung,
                        'funktion' => $team->funktion ? $team->funktion->Bezeichnung : null
                    ];
                });

            // If no data found, return fallback message
            if ($sachbereiche->isEmpty()) {
                return response()->json([['value' => 'Keine Daten gefunden', 'label' => 'Keine Daten gefunden', 'name' => 'Keine Daten gefunden']]);
            }

            return response()->json($sachbereiche);
        } catch (\Exception $e) {
            return response()->json([['value' => 'Keine Daten gefunden', 'label' => 'Keine Daten gefunden', 'name' => 'Keine Daten gefunden']]);
        }
    }
}