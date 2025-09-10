<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Position;

class PositionController extends Controller
{
    public function index()
    {
        try {
            $positionen = Position::orderBy('Bezeichnung')
                ->get()
                ->map(function($position) {
                    return [
                        'value' => $position->Bezeichnung,
                        'label' => $position->Bezeichnung,
                        'id' => $position->PositionID,
                        'name' => $position->Bezeichnung
                    ];
                });

            // If no data found, return fallback message
            if ($positionen->isEmpty()) {
                return response()->json([['value' => 'Keine Daten gefunden', 'label' => 'Keine Daten gefunden', 'name' => 'Keine Daten gefunden']]);
            }

            return response()->json($positionen);
        } catch (\Exception $e) {
            return response()->json([['value' => 'Keine Daten gefunden', 'label' => 'Keine Daten gefunden', 'name' => 'Keine Daten gefunden']]);
        }
    }
}