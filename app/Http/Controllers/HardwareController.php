<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\Hardware;
use App\Models\Referenz;

class HardwareController extends Controller
{
    /**
     * Get hardware items based on reference profile
     */
    public function getHardwareByProfile(Request $request): JsonResponse
    {
        $profileName = $request->input('profile');
        
        if (!$profileName) {
            return response()->json(['error' => 'Profile parameter is required'], 400);
        }

        try {
            $referenz = Referenz::with('hardware')
                ->where('Bezeichnung', $profileName)
                ->first();
            
            if (!$referenz) {
                return response()->json(['error' => 'Profile not found'], 404);
            }

            $hardware = $referenz->hardware->map(function($item) {
                return [
                    'id' => $item->HardwareID,
                    'name' => $item->Bezeichnung,
                    'category' => 'Hardware',
                    'specifications' => $item->Hersteller,
                    'assigned' => true
                ];
            });

            return response()->json([
                'profile' => $profileName,
                'hardware' => $hardware
            ]);
            
        } catch (\Exception $e) {
            return response()->json(['error' => 'Internal server error'], 500);
        }
    }

    /**
     * Get all available hardware items
     */
    public function getAllHardware(): JsonResponse
    {
        try {
            $hardware = Hardware::orderBy('Bezeichnung')
                ->get()
                ->map(function($item) {
                    return [
                        'id' => $item->HardwareID,
                        'name' => $item->Bezeichnung,
                        'category' => 'Hardware',
                        'specifications' => $item->Hersteller,
                        'assigned' => false
                    ];
                });

            return response()->json([
                'hardware' => $hardware
            ]);
            
        } catch (\Exception $e) {
            return response()->json(['error' => 'Internal server error'], 500);
        }
    }

    /**
     * Get additional hardware items for selection
     */
    public function getAdditionalHardware(): JsonResponse
    {
        try {
            // Get hardware that is not part of any reference profile
            $hardware = Hardware::whereDoesntHave('referenzen')
                ->orderBy('Bezeichnung')
                ->get()
                ->map(function($item) {
                    return [
                        'id' => $item->HardwareID,
                        'name' => $item->Bezeichnung,
                        'category' => 'Hardware',
                        'specifications' => $item->Hersteller,
                        'assigned' => false
                    ];
                });

            return response()->json([
                'hardware' => $hardware
            ]);
            
        } catch (\Exception $e) {
            return response()->json(['error' => 'Internal server error'], 500);
        }
    }

    /**
     * Get hardware categories
     */
    public function getCategories(): JsonResponse
    {
        try {
            // Get unique manufacturers as categories
            $categories = Hardware::distinct('Hersteller')
                ->orderBy('Hersteller')
                ->pluck('Hersteller')
                ->filter()
                ->values();

            return response()->json([
                'categories' => $categories
            ]);
            
        } catch (\Exception $e) {
            return response()->json(['error' => 'Internal server error'], 500);
        }
    }

    /**
     * Get hardware by category
     */
    public function getHardwareByCategory(Request $request): JsonResponse
    {
        $category = $request->input('category');
        
        if (!$category) {
            return response()->json(['error' => 'Category parameter is required'], 400);
        }

        try {
            $hardware = Hardware::where('Hersteller', $category)
                ->orderBy('Bezeichnung')
                ->get()
                ->map(function($item) {
                    return [
                        'id' => $item->HardwareID,
                        'name' => $item->Bezeichnung,
                        'category' => 'Hardware',
                        'specifications' => $item->Hersteller,
                        'assigned' => false
                    ];
                });

            if ($hardware->isEmpty()) {
                return response()->json(['error' => 'No hardware found for this category'], 404);
            }

            return response()->json([
                'category' => $category,
                'hardware' => $hardware
            ]);
            
        } catch (\Exception $e) {
            return response()->json(['error' => 'Internal server error'], 500);
        }
    }

    /**
     * Search hardware with live search functionality
     */
    public function searchHardware(Request $request): JsonResponse
    {
        $query = $request->input('query', '');
        
        try {
            $hardwareQuery = Hardware::query();
                
            if (!empty(trim($query))) {
                $searchQuery = trim($query);
                $hardwareQuery->where(function($q) use ($searchQuery) {
                    $q->where('Bezeichnung', 'like', "%{$searchQuery}%")
                      ->orWhere('Hersteller', 'like', "%{$searchQuery}%");
                });
            }
            
            $hardware = $hardwareQuery->orderBy('Bezeichnung')
                ->get()
                ->map(function($item) {
                    return [
                        'id' => $item->HardwareID,
                        'name' => $item->Bezeichnung,
                        'category' => 'Hardware',
                        'specifications' => $item->Hersteller,
                        'assigned' => false
                    ];
                });
            
            // Group by manufacturer for organized display
            $groupedResults = [];
            foreach ($hardware as $item) {
                $category = $item['specifications']; // Using manufacturer as category
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
                'total_results' => $hardware->count()
            ]);
            
        } catch (\Exception $e) {
            return response()->json(['error' => 'Internal server error'], 500);
        }
    }
}