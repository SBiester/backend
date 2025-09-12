<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\Referenz;
use App\Models\Bereich;

class ReferenzprofilController extends Controller
{
    /**
     * Get all reference profiles
     */
    public function index(): JsonResponse
    {
        try {
            $referenzprofile = Referenz::with(['bereich.teams', 'software', 'hardware', 'sammelrollen'])
                ->where('aktiv', true)
                ->orderBy('Bezeichnung')
                ->get()
                ->map(function($referenz) {
                    $team = null;
                    if ($referenz->bereich && $referenz->bereich->teams->isNotEmpty()) {
                        // Get first team of the bereich as default
                        $team = $referenz->bereich->teams->first()->Bezeichnung ?? null;
                    }
                    
                    return [
                        'id' => $referenz->ReferenzID,
                        'name' => $referenz->Bezeichnung,
                        'bereich' => $referenz->bereich ? $referenz->bereich->Bezeichnung : null,
                        'team' => $team,
                        'category' => 'Allgemein',
                        'description' => 'Referenzprofil für ' . $referenz->Bezeichnung,
                        'softwareCount' => $referenz->software->count(),
                        'hardwareCount' => $referenz->hardware->count(),
                        'sapProfileCount' => $referenz->sammelrollen->count()
                    ];
                });

            return response()->json($referenzprofile);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to fetch reference profiles'], 500);
        }
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

        try {
            $referenzprofile = Referenz::with(['bereich', 'software', 'hardware', 'sammelrollen'])
                ->whereHas('bereich', function($query) use ($bereich) {
                    $query->where('Bezeichnung', 'like', "%{$bereich}%");
                })
                ->where('aktiv', true)
                ->orderBy('Bezeichnung')
                ->get()
                ->map(function($referenz) {
                    return [
                        'id' => $referenz->ReferenzID,
                        'name' => $referenz->Bezeichnung,
                        'bereich' => $referenz->bereich ? $referenz->bereich->Bezeichnung : null,
                        'description' => 'Referenzprofil für ' . $referenz->Bezeichnung,
                        'softwareCount' => $referenz->software->count(),
                        'hardwareCount' => $referenz->hardware->count(),
                        'sapProfileCount' => $referenz->sammelrollen->count()
                    ];
                });

            return response()->json($referenzprofile->values());
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to fetch reference profiles'], 500);
        }
    }

    /**
     * Create a new reference profile
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validatedData = $request->validate([
                'name' => 'required|string|max:255',
                'bereich_id' => 'nullable|integer|exists:tbl_bereich,BereichID',
                'description' => 'nullable|string',
                'team' => 'nullable|string', // Accept team but don't save (for future compatibility)
                'aktiv' => 'boolean',
                'hardwareItems' => 'array',
                'hardwareItems.*' => 'integer',
                'softwareItems' => 'array', 
                'softwareItems.*' => 'integer',
                'sapItems' => 'array',
                'sapItems.*' => 'integer'
            ]);

            $referenz = Referenz::create([
                'Bezeichnung' => $validatedData['name'],
                'BereichID' => $validatedData['bereich_id'],
                'aktiv' => $validatedData['aktiv'] ?? true
            ]);

            // Attach hardware relationships
            if (isset($validatedData['hardwareItems']) && !empty($validatedData['hardwareItems'])) {
                $referenz->hardware()->sync($validatedData['hardwareItems']);
            }

            // Attach software relationships
            if (isset($validatedData['softwareItems']) && !empty($validatedData['softwareItems'])) {
                $referenz->software()->sync($validatedData['softwareItems']);
            }

            // Attach SAP profile relationships
            if (isset($validatedData['sapItems']) && !empty($validatedData['sapItems'])) {
                $referenz->sammelrollen()->sync($validatedData['sapItems']);
            }

            // Load relationships for response
            $referenz->load(['bereich', 'hardware', 'software', 'sammelrollen']);

            return response()->json([
                'message' => 'Reference profile created successfully', 
                'profile' => [
                    'id' => $referenz->ReferenzID,
                    'name' => $referenz->Bezeichnung,
                    'bereich_id' => $referenz->BereichID,
                    'aktiv' => $referenz->aktiv,
                    'hardwareCount' => $referenz->hardware->count(),
                    'softwareCount' => $referenz->software->count(),
                    'sapProfileCount' => $referenz->sammelrollen->count()
                ]
            ], 201);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to create profile: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Update a reference profile
     */
    public function update(Request $request, $id): JsonResponse
    {
        try {
            $referenz = Referenz::find($id);
            if (!$referenz) {
                return response()->json(['error' => 'Profile not found'], 404);
            }

            $validatedData = $request->validate([
                'name' => 'required|string|max:255',
                'bereich_id' => 'nullable|integer|exists:tbl_bereich,BereichID',
                'team' => 'nullable|string', // Accept team but don't save (for future compatibility)
                'aktiv' => 'boolean',
                'hardwareItems' => 'array',
                'hardwareItems.*' => 'integer',
                'softwareItems' => 'array', 
                'softwareItems.*' => 'integer',
                'sapItems' => 'array',
                'sapItems.*' => 'integer'
            ]);

            // Update basic profile information
            $referenz->update([
                'Bezeichnung' => $validatedData['name'],
                'BereichID' => $validatedData['bereich_id'] ?? $referenz->BereichID,
                'aktiv' => $validatedData['aktiv'] ?? $referenz->aktiv
            ]);

            // Update hardware relationships
            if (isset($validatedData['hardwareItems'])) {
                $referenz->hardware()->sync($validatedData['hardwareItems']);
            }

            // Update software relationships
            if (isset($validatedData['softwareItems'])) {
                $referenz->software()->sync($validatedData['softwareItems']);
            }

            // Update SAP profile relationships
            if (isset($validatedData['sapItems'])) {
                $referenz->sammelrollen()->sync($validatedData['sapItems']);
            }

            // Reload with relationships for response
            $referenz->load(['bereich', 'hardware', 'software', 'sammelrollen']);

            return response()->json([
                'message' => 'Profile updated successfully', 
                'profile' => [
                    'id' => $referenz->ReferenzID,
                    'name' => $referenz->Bezeichnung,
                    'bereich_id' => $referenz->BereichID,
                    'aktiv' => $referenz->aktiv,
                    'hardwareCount' => $referenz->hardware->count(),
                    'softwareCount' => $referenz->software->count(),
                    'sapProfileCount' => $referenz->sammelrollen->count()
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to update profile: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Delete a reference profile
     */
    public function destroy($id): JsonResponse
    {
        try {
            $referenz = Referenz::find($id);
            if (!$referenz) {
                return response()->json(['error' => 'Profile not found'], 404);
            }

            // Soft delete by setting aktiv to false
            $referenz->update(['aktiv' => false]);

            return response()->json(['message' => 'Profile deactivated successfully']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to delete profile'], 500);
        }
    }

    /**
     * Get profiles with search and filtering for admin
     */
    public function adminIndex(Request $request): JsonResponse
    {
        try {
            $query = Referenz::with([
                'bereich.teams', 
                'software.hersteller', 
                'hardware.kategorie', 
                'sammelrollen.rollengruppe'
            ]);

            // Apply search filter
            if ($request->has('search') && !empty($request->search)) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('Bezeichnung', 'like', "%{$search}%")
                      ->orWhereHas('bereich', function($bq) use ($search) {
                          $bq->where('Bezeichnung', 'like', "%{$search}%");
                      });
                });
            }

            // Apply bereich filter
            if ($request->has('bereich') && !empty($request->bereich)) {
                $query->whereHas('bereich', function($bq) use ($request) {
                    $bq->where('Bezeichnung', $request->bereich);
                });
            }

            // Apply status filter
            if ($request->has('status')) {
                $aktiv = $request->status === 'aktiv';
                $query->where('aktiv', $aktiv);
            }

            // Pagination
            $page = $request->get('page', 1);
            $perPage = $request->get('per_page', 20);
            
            $profiles = $query->paginate($perPage, ['*'], 'page', $page);

            $transformedData = $profiles->getCollection()->map(function($referenz) {
                $team = null;
                if ($referenz->bereich && $referenz->bereich->teams->isNotEmpty()) {
                    // Get first team of the bereich as default
                    $team = $referenz->bereich->teams->first()->Bezeichnung ?? null;
                }
                
                return [
                    'id' => $referenz->ReferenzID,
                    'name' => $referenz->Bezeichnung,
                    'bereich' => $referenz->bereich ? $referenz->bereich->Bezeichnung : 'Unbekannt',
                    'team' => $team,
                    'category' => 'Allgemein',
                    'description' => 'Referenzprofil für ' . $referenz->Bezeichnung,
                    'hardwareCount' => $referenz->hardware->count(),
                    'softwareCount' => $referenz->software->count(),
                    'sapProfileCount' => $referenz->sammelrollen->count(),
                    'status' => $referenz->aktiv ? 'aktiv' : 'inaktiv',
                    'isTemplate' => true,
                    'created_at' => $referenz->created_at ? $referenz->created_at->format('Y-m-d') : now()->format('Y-m-d'),
                    'hardwareItems' => $referenz->hardware->map(function($hardware) {
                        return [
                            'id' => $hardware->HardwareID,
                            'name' => $hardware->Bezeichnung,
                            'category' => $hardware->kategorie ? $hardware->kategorie->Bezeichnung : 'Unbekannt'
                        ];
                    }),
                    'softwareItems' => $referenz->software->map(function($software) {
                        return [
                            'id' => $software->SoftwareID,
                            'name' => $software->Bezeichnung,
                            'manufacturer' => $software->hersteller ? $software->hersteller->Bezeichnung : 'Unbekannt'
                        ];
                    }),
                    'sapItems' => $referenz->sammelrollen->map(function($sammelrolle) {
                        return [
                            'id' => $sammelrolle->SammelrollenID,
                            'Bezeichnung' => $sammelrolle->Bezeichnung,
                            'Rollengruppe' => $sammelrolle->rollengruppe ? [
                                'Bezeichnung' => $sammelrolle->rollengruppe->Bezeichnung
                            ] : null
                        ];
                    })
                ];
            });

            return response()->json([
                'data' => $transformedData,
                'current_page' => $profiles->currentPage(),
                'last_page' => $profiles->lastPage(),
                'per_page' => $profiles->perPage(),
                'total' => $profiles->total()
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to load profiles'], 500);
        }
    }
}