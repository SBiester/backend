<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Bereich;

class BereichController extends Controller
{
    public function index()
    {
        try {
            $bereiche = Bereich::with(['team.funktion'])
                ->orderBy('Bezeichnung')
                ->get()
                ->map(function($bereich) {
                    return [
                        'value' => $bereich->Bezeichnung,
                        'label' => $bereich->Bezeichnung,
                        'id' => $bereich->BereichID,
                        'name' => $bereich->Bezeichnung,
                        'team' => $bereich->team ? $bereich->team->Bezeichnung : null,
                        'funktion' => $bereich->team && $bereich->team->funktion ? $bereich->team->funktion->Bezeichnung : null
                    ];
                });

            // If no data found, return fallback message
            if ($bereiche->isEmpty()) {
                return response()->json([['value' => 'Keine Daten gefunden', 'label' => 'Keine Daten gefunden', 'name' => 'Keine Daten gefunden']]);
            }

            return response()->json($bereiche);
        } catch (\Exception $e) {
            return response()->json([['value' => 'Keine Daten gefunden', 'label' => 'Keine Daten gefunden', 'name' => 'Keine Daten gefunden']]);
        }
    }
}