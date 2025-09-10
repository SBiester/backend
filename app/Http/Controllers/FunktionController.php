<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Funktion;

class FunktionController extends Controller
{
    public function index()
    {
        try {
            $funktionen = Funktion::orderBy('Bezeichnung')
                ->get()
                ->map(function($funktion) {
                    return [
                        'value' => $funktion->Bezeichnung,
                        'label' => $funktion->Bezeichnung,
                        'id' => $funktion->FunktionID,
                        'name' => $funktion->Bezeichnung
                    ];
                });

            // If no data found, return fallback message
            if ($funktionen->isEmpty()) {
                return response()->json([['value' => 'Keine Daten gefunden', 'label' => 'Keine Daten gefunden', 'name' => 'Keine Daten gefunden']]);
            }

            return response()->json($funktionen);
        } catch (\Exception $e) {
            return response()->json([['value' => 'Keine Daten gefunden', 'label' => 'Keine Daten gefunden', 'name' => 'Keine Daten gefunden']]);
        }
    }
}