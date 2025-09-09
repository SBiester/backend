<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class OptionsController extends Controller
{
    /**
     * Später durch Daten aus der Datenbank ersetzen
     */
    public function getOptions(): JsonResponse
    {
        $options = [
            [
                'id' => 'telefonnummer',
                'name' => 'Telefonnummer',
                'description' => 'Firmennummer mit persönlicher Durchwahl',
                'icon' => ''
            ],
            [
                'id' => 'tuerschild',
                'name' => 'Türschild',
                'description' => 'Namensschild für Büro oder Arbeitsplatz',
                'icon' => ''
            ],
            [
                'id' => 'visitenkarten',
                'name' => 'Visitenkarten',
                'description' => 'Persönliche Visitenkarten mit Firmenlayout',
                'icon' => ''
            ]
        ];

        return response()->json($options);
    }

    /**
     * Get available telefon types
     * Später durch Daten aus der Datenbank ersetzen
     */
    public function getTelefonTypes(): JsonResponse
    {
        $telefonTypes = [
            [
                'id' => 'standard',
                'name' => 'Standard',
                'description' => 'Standard Telefon mit Grundfunktionen'
            ],
            [
                'id' => 'komfort',
                'name' => 'Komfort',
                'description' => 'Komfort Telefon mit erweiterten Funktionen'
            ]
        ];

        return response()->json($telefonTypes);
    }
}