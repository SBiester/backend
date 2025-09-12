<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\Software;
use App\Models\Hersteller;
use App\Models\Kategorie;
use App\Models\Referenz;

class SoftwareController extends Controller
{
    /**
     * Get software items based on reference profile
     */
    public function getSoftwareByProfile(Request $request): JsonResponse
    {
        $profileName = $request->input('profile');
        
        if (!$profileName) {
            return response()->json(['error' => 'Profile parameter is required'], 400);
        }

        try {
            $referenz = Referenz::with(['software.hersteller'])
                ->where('Bezeichnung', $profileName)
                ->first();
            
            if (!$referenz) {
                return response()->json(['error' => 'Profile not found'], 404);
            }

            $software = $referenz->software->map(function($item) {
                return [
                    'id' => $item->SoftwareID,
                    'name' => $item->Bezeichnung,
                    'category' => 'Software',
                    'manufacturer' => $item->hersteller ? $item->hersteller->Bezeichnung : 'Unbekannt',
                    'description' => $item->Bezeichnung,
                    'aktiv' => $item->aktiv
                ];
            });

            return response()->json([
                'profile' => $profileName,
                'software' => $software
            ]);
            
        } catch (\Exception $e) {
            return response()->json(['error' => 'Internal server error'], 500);
        }
    }

    /**
     * Get all available software items
     */
    public function getAllSoftware(): JsonResponse
    {
        try {
            $software = Software::with(['hersteller', 'kategorie'])
                ->where('aktiv', true)
                ->get()
                ->map(function($item) {
                    return [
                        'id' => $item->SoftwareID,
                        'name' => $item->Bezeichnung,
                        'category' => $item->kategorie ? $item->kategorie->Bezeichnung : 'Unbekannt',
                        'manufacturer' => $item->hersteller ? $item->hersteller->Bezeichnung : 'Unbekannt',
                        'description' => $item->Bezeichnung,
                        'aktiv' => $item->aktiv
                    ];
                });

            return response()->json([
                'software' => $software
            ]);
            
        } catch (\Exception $e) {
            return response()->json(['error' => 'Internal server error'], 500);
        }
    }

    /**
     * Get additional software items for selection
     */
    public function getAdditionalSoftware(): JsonResponse
    {
        try {
            // Get software that is not part of any reference profile
            $software = Software::with(['hersteller', 'kategorie'])
                ->where('aktiv', true)
                ->whereDoesntHave('referenzen')
                ->get()
                ->map(function($item) {
                    return [
                        'id' => $item->SoftwareID,
                        'name' => $item->Bezeichnung,
                        'category' => $item->kategorie ? $item->kategorie->Bezeichnung : 'Unbekannt',
                        'manufacturer' => $item->hersteller ? $item->hersteller->Bezeichnung : 'Unbekannt',
                        'description' => $item->Bezeichnung,
                        'aktiv' => $item->aktiv
                    ];
                });

            return response()->json([
                'software' => $software
            ]);
            
        } catch (\Exception $e) {
            return response()->json(['error' => 'Internal server error'], 500);
        }
    }

    /**
     * Get software manufacturers
     */
    public function getManufacturers(): JsonResponse
    {
        try {
            $manufacturers = Hersteller::orderBy('Bezeichnung')->pluck('Bezeichnung');

            return response()->json([
                'manufacturers' => $manufacturers
            ]);
            
        } catch (\Exception $e) {
            return response()->json(['error' => 'Internal server error'], 500);
        }
    }

    /**
     * Get software by manufacturer
     */
    public function getSoftwareByManufacturer(Request $request): JsonResponse
    {
        $manufacturer = $request->input('manufacturer');
        
        if (!$manufacturer) {
            return response()->json(['error' => 'Manufacturer parameter is required'], 400);
        }

        try {
            $software = Software::with(['hersteller', 'kategorie'])
                ->whereHas('hersteller', function($query) use ($manufacturer) {
                    $query->where('Bezeichnung', $manufacturer);
                })
                ->where('aktiv', true)
                ->get()
                ->map(function($item) {
                    return [
                        'id' => $item->SoftwareID,
                        'name' => $item->Bezeichnung,
                        'category' => $item->kategorie ? $item->kategorie->Bezeichnung : 'Unbekannt',
                        'manufacturer' => $item->hersteller ? $item->hersteller->Bezeichnung : 'Unbekannt',
                        'description' => $item->Bezeichnung,
                        'aktiv' => $item->aktiv
                    ];
                });

            if ($software->isEmpty()) {
                return response()->json(['error' => 'No software found for this manufacturer'], 404);
            }

            return response()->json([
                'manufacturer' => $manufacturer,
                'software' => $software
            ]);
            
        } catch (\Exception $e) {
            return response()->json(['error' => 'Internal server error'], 500);
        }
    }


    /**
     * Search software with live search functionality
     */
    public function searchSoftware(Request $request): JsonResponse
    {
        $query = $request->input('query', '');
        
        try {
            $softwareQuery = Software::with(['hersteller', 'kategorie'])
                ->where('aktiv', true);
                
            if (!empty(trim($query))) {
                $searchQuery = trim($query);
                $softwareQuery->where(function($q) use ($searchQuery) {
                    $q->where('Bezeichnung', 'like', "%{$searchQuery}%")
                      ->orWhereHas('kategorie', function($kq) use ($searchQuery) {
                          $kq->where('Bezeichnung', 'like', "%{$searchQuery}%");
                      })
                      ->orWhereHas('hersteller', function($hq) use ($searchQuery) {
                          $hq->where('Bezeichnung', 'like', "%{$searchQuery}%");
                      });
                });
            }
            
            $software = $softwareQuery->get()
                ->map(function($item) {
                    return [
                        'id' => $item->SoftwareID,
                        'name' => $item->Bezeichnung,
                        'category' => $item->kategorie ? $item->kategorie->Bezeichnung : 'Unbekannt',
                        'manufacturer' => $item->hersteller ? $item->hersteller->Bezeichnung : 'Unbekannt',
                        'description' => $item->Bezeichnung,
                        'aktiv' => $item->aktiv
                    ];
                });
            
            // Group by category for organized display
            $groupedResults = [];
            foreach ($software as $item) {
                $category = $item['category'];
                if (!isset($groupedResults[$category])) {
                    $groupedResults[$category] = [
                        'category' => $category,
                        'items' => []
                    ];
                }
                $groupedResults[$category]['items'][] = $item;
            }
            
            return response()->json([
                'categories' => array_values($groupedResults),
                'query' => $query,
                'total_results' => $software->count()
            ]);
            
        } catch (\Exception $e) {
            return response()->json(['error' => 'Internal server error'], 500);
        }
    }
}