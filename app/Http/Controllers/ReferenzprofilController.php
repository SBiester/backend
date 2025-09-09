<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ReferenzprofilController extends Controller
{
    /**
     * Get all reference profiles
     */
    public function index(): JsonResponse
    {
        $referenzprofile = [
            ['id' => 1, 'name' => 'Wirschinin Elena', 'bereich' => 'IT', 'description' => 'IT Projektmanagement'],
            ['id' => 2, 'name' => 'Königs Maik', 'bereich' => 'IT', 'description' => 'IT Entwicklung'],
            ['id' => 3, 'name' => 'Böhm Finn', 'bereich' => 'IT', 'description' => 'IT Infrastruktur/Betrieb'],
            ['id' => 4, 'name' => 'Amann Korbinian', 'bereich' => 'IT', 'description' => 'IT Administration/Support'],
            ['id' => 5, 'name' => 'Wegener Jennifer', 'bereich' => 'TK', 'description' => 'Telekommunikation Projektmanagement'],
            ['id' => 6, 'name' => 'Breidenbicher Kjell', 'bereich' => 'TK', 'description' => 'Telekommunikation Operations'],
            ['id' => 7, 'name' => 'Dobbrunz Sven', 'bereich' => 'PM', 'description' => 'Projektmanagement Manager'],
            ['id' => 8, 'name' => 'Saed Abdo', 'bereich' => 'PM', 'description' => 'Projektmanagement Assistent'],
            ['id' => 9, 'name' => 'Riedel Carsten', 'bereich' => 'MK', 'description' => 'Marketing Leitung']
        ];

        return response()->json($referenzprofile);
    }

    /**
     * Get reference profiles by Bereich
     */
    public function getByBereich(Request $request): JsonResponse
    {
        $bereich = $request->input('bereich');
        
        if (!$bereich) {
            return response()->json(['error' => 'Bereich parameter is required'], 400);
        }

        $allProfiles = [
            ['id' => 1, 'name' => 'Wirschinin Elena', 'bereich' => 'IT', 'description' => 'IT Projektmanagement'],
            ['id' => 2, 'name' => 'Königs Maik', 'bereich' => 'IT', 'description' => 'IT Entwicklung'],
            ['id' => 3, 'name' => 'Böhm Finn', 'bereich' => 'IT', 'description' => 'IT Infrastruktur/Betrieb'],
            ['id' => 4, 'name' => 'Amann Korbinian', 'bereich' => 'IT', 'description' => 'IT Administration/Support'],
            ['id' => 5, 'name' => 'Wegener Jennifer', 'bereich' => 'TK', 'description' => 'Telekommunikation Projektmanagement'],
            ['id' => 6, 'name' => 'Breidenbicher Kjell', 'bereich' => 'TK', 'description' => 'Telekommunikation Operations'],
            ['id' => 7, 'name' => 'Dobbrunz Sven', 'bereich' => 'PM', 'description' => 'Projektmanagement Manager'],
            ['id' => 8, 'name' => 'Saed Abdo', 'bereich' => 'PM', 'description' => 'Projektmanagement Assistent'],
            ['id' => 9, 'name' => 'Riedel Carsten', 'bereich' => 'MK', 'description' => 'Marketing Leitung']
        ];

        $filteredProfiles = array_filter($allProfiles, function($profile) use ($bereich) {
            return strtoupper($profile['bereich']) === strtoupper($bereich);
        });

        return response()->json(array_values($filteredProfiles));
    }
}