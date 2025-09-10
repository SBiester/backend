<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\Rollengruppe;
use App\Models\Sammelrollen;

class SapController extends Controller
{
    /**
     * Get all SAP profile groups with their profiles
     */
    public function getSapProfiles(): JsonResponse
    {
        return response()->json([
            'groups' => $this->getSapProfileGroups()
        ]);
    }

    /**
     * Get SAP profile groups by category
     */
    public function getSapProfilesByCategory(Request $request): JsonResponse
    {
        $category = $request->input('category');
        
        if (!$category) {
            return response()->json(['error' => 'Category parameter is required'], 400);
        }

        $profileGroups = $this->getSapProfileGroups();
        $filteredGroups = array_filter($profileGroups, function($group) use ($category) {
            return strtolower($group['name']) === strtolower($category);
        });

        if (empty($filteredGroups)) {
            return response()->json(['error' => 'Category not found'], 404);
        }

        return response()->json([
            'category' => $category,
            'groups' => array_values($filteredGroups)
        ]);
    }

    /**
     * Get a specific SAP profile by ID
     */
    public function getSapProfileById(Request $request): JsonResponse
    {
        $profileId = $request->input('id');
        
        if (!$profileId) {
            return response()->json(['error' => 'Profile ID parameter is required'], 400);
        }

        $allProfiles = $this->getAllSapProfiles();
        $profile = collect($allProfiles)->firstWhere('id', (int)$profileId);

        if (!$profile) {
            return response()->json(['error' => 'Profile not found'], 404);
        }

        return response()->json([
            'profile' => $profile
        ]);
    }

    /**
     * Get all available SAP profile categories from database
     */
    public function getSapCategories(): JsonResponse
    {
        $gruppen = Rollengruppe::withCount('sammelrollen')->get();
        
        $categories = $gruppen->map(function($gruppe) {
            return [
                'id' => $gruppe->RollengruppeID,
                'name' => $gruppe->Bezeichnung,
                'description' => $gruppe->Bezeichnung,
                'profile_count' => $gruppe->sammelrollen_count
            ];
        });

        return response()->json([
            'categories' => $categories
        ]);
    }

    /**
     * Get SAP profile groups from database
     */
    private function getSapProfileGroups(): array
    {
        $gruppen = Rollengruppe::with('sammelrollen')->get();
        
        $profileGroups = [];
        
        foreach ($gruppen as $gruppe) {
            $profiles = [];
            
            foreach ($gruppe->sammelrollen as $rolle) {
                $profiles[] = [
                    'id' => $rolle->SammelrollenID,
                    'name' => $rolle->Bezeichnung,
                    'code' => $rolle->Schluessel,
                    'description' => $rolle->Bezeichnung,
                    'permissions' => []
                ];
            }
            
            $profileGroups[] = [
                'id' => $gruppe->RollengruppeID,
                'name' => $gruppe->Bezeichnung,
                'description' => $gruppe->Bezeichnung,
                'profiles' => $profiles
            ];
        }
        
        return $profileGroups;
    }


    /**
     * Get all SAP profiles as flat array
     */
    private function getAllSapProfiles(): array
    {
        $allProfiles = [];
        foreach ($this->getSapProfileGroups() as $group) {
            foreach ($group['profiles'] as $profile) {
                $profile['group_name'] = $group['name'];
                $profile['group_id'] = $group['id'];
                $allProfiles[] = $profile;
            }
        }
        return $allProfiles;
    }

    /**
     * Get profile statistics from database
     */
    public function getSapStatistics(): JsonResponse
    {
        $totalGroups = Rollengruppe::count();
        $totalProfiles = Sammelrollen::count();
        
        $groups = Rollengruppe::withCount('sammelrollen')->get();
        
        $groupsBreakdown = $groups->map(function($gruppe) {
            return [
                'name' => $gruppe->Bezeichnung,
                'profile_count' => $gruppe->sammelrollen_count
            ];
        });

        return response()->json([
            'statistics' => [
                'total_groups' => $totalGroups,
                'total_profiles' => $totalProfiles,
                'total_permissions' => 0,
                'groups_breakdown' => $groupsBreakdown
            ]
        ]);
    }

    /**
     * Get all SAP profiles directly from database
     */
    public function getAllSapProfilesFromDb(): JsonResponse
    {
        $sammelrollen = Sammelrollen::with('rollengruppe')->get();
        
        $profiles = $sammelrollen->map(function($rolle) {
            return [
                'id' => $rolle->SammelrollenID,
                'name' => $rolle->Bezeichnung,
                'code' => $rolle->Schluessel,
                'description' => $rolle->Bezeichnung,
                'group_id' => $rolle->RollengruppeID,
                'group_name' => $rolle->rollengruppe->Bezeichnung ?? 'Unbekannt'
            ];
        });
        
        return response()->json([
            'profiles' => $profiles
        ]);
    }

    /**
     * Get SAP groups directly from database
     */
    public function getSapGroupsFromDb(): JsonResponse
    {
        $gruppen = Rollengruppe::withCount('sammelrollen')->get();
        
        $groups = $gruppen->map(function($gruppe) {
            return [
                'id' => $gruppe->RollengruppeID,
                'name' => $gruppe->Bezeichnung,
                'description' => $gruppe->Bezeichnung,
                'profile_count' => $gruppe->sammelrollen_count
            ];
        });
        
        return response()->json([
            'groups' => $groups
        ]);
    }

    /**
     * Search SAP profiles with live search functionality
     */
    public function searchSapProfiles(Request $request): JsonResponse
    {
        $query = $request->input('query', '');
        
        if (empty(trim($query))) {
            return $this->getSapProfiles();
        }
        
        $searchQuery = strtolower(trim($query));
        
        // Search in database
        $gruppen = Rollengruppe::with(['sammelrollen' => function($q) use ($searchQuery) {
            $q->whereRaw('LOWER(Bezeichnung) LIKE ?', ["%{$searchQuery}%"])
              ->orWhereRaw('LOWER(Schluessel) LIKE ?', ["%{$searchQuery}%"]);
        }])->get();
        
        // Filter groups that have no matching profiles after search
        $gruppen = $gruppen->filter(function($gruppe) use ($searchQuery) {
            return $gruppe->sammelrollen->count() > 0 || 
                   str_contains(strtolower($gruppe->Bezeichnung), $searchQuery);
        });
        
        $profileGroups = [];
        
        foreach ($gruppen as $gruppe) {
            $profiles = [];
            
            // If group name matches, include all profiles
            if (str_contains(strtolower($gruppe->Bezeichnung), $searchQuery)) {
                $allRollen = Sammelrollen::where('RollengruppeID', $gruppe->RollengruppeID)->get();
                foreach ($allRollen as $rolle) {
                    $profiles[] = [
                        'id' => $rolle->SammelrollenID,
                        'name' => $rolle->Bezeichnung,
                        'code' => $rolle->Schluessel,
                        'description' => $rolle->Bezeichnung,
                        'permissions' => []
                    ];
                }
            } else {
                // Only include matching profiles
                foreach ($gruppe->sammelrollen as $rolle) {
                    $profiles[] = [
                        'id' => $rolle->SammelrollenID,
                        'name' => $rolle->Bezeichnung,
                        'code' => $rolle->Schluessel,
                        'description' => $rolle->Bezeichnung,
                        'permissions' => []
                    ];
                }
            }
            
            if (!empty($profiles)) {
                $profileGroups[] = [
                    'id' => $gruppe->RollengruppeID,
                    'name' => $gruppe->Bezeichnung,
                    'description' => $gruppe->Bezeichnung,
                    'profiles' => $profiles
                ];
            }
        }
        
        return response()->json([
            'groups' => $profileGroups,
            'query' => $query,
            'total_results' => collect($profileGroups)->sum(function($group) {
                return count($group['profiles']);
            })
        ]);
    }
}