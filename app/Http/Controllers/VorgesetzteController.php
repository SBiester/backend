<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Mitarbeiter;

class VorgesetzteController extends Controller
{
    public function index()
    {
        try {
            // Get unique supervisors from employee data
            $vorgesetzte = Mitarbeiter::whereNotNull('Vorgesetzter')
                ->distinct('Vorgesetzter')
                ->orderBy('Vorgesetzter')
                ->pluck('Vorgesetzter')
                ->map(function($vorgesetzter) {
                    return [
                        'value' => $vorgesetzter,
                        'label' => $vorgesetzter,
                        'name' => $vorgesetzter
                    ];
                });

            // If no data found, return fallback message
            if ($vorgesetzte->isEmpty()) {
                return response()->json([['value' => 'Keine Daten gefunden', 'label' => 'Keine Daten gefunden', 'name' => 'Keine Daten gefunden']]);
            }

            return response()->json($vorgesetzte);
        } catch (\Exception $e) {
            return response()->json([['value' => 'Keine Daten gefunden', 'label' => 'Keine Daten gefunden', 'name' => 'Keine Daten gefunden']]);
        }
    }
}