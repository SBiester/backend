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
            $referenzprofile = Referenz::with(['bereich.team.funktion', 'software', 'hardware', 'sammelrollen'])
                ->where('aktiv', true)
                ->orderBy('Bezeichnung')
                ->get()
                ->map(function($referenz) {
                    return [
                        'id' => $referenz->ReferenzID,
                        'name' => $referenz->Bezeichnung,
                        'bereich' => $referenz->bereich ? $referenz->bereich->Bezeichnung : null,
                        'category' => $referenz->bereich && $referenz->bereich->team && $referenz->bereich->team->funktion 
                            ? $referenz->bereich->team->funktion->Bezeichnung : 'Allgemein',
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
            $referenzprofile = Referenz::with(['bereich.team.funktion', 'software', 'hardware', 'sammelrollen'])
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
                'bereich_id' => 'required|integer|exists:tbl_bereich,BereichID',
                'description' => 'nullable|string',
                'aktiv' => 'boolean'
            ]);

            $referenz = Referenz::create([
                'Bezeichnung' => $validatedData['name'],
                'BereichID' => $validatedData['bereich_id'],
                'aktiv' => $validatedData['aktiv'] ?? true
            ]);

            return response()->json([
                'message' => 'Reference profile created successfully', 
                'profile' => [
                    'id' => $referenz->ReferenzID,
                    'name' => $referenz->Bezeichnung,
                    'bereich_id' => $referenz->BereichID,
                    'aktiv' => $referenz->aktiv
                ]
            ], 201);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to create profile'], 500);
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
                'bereich_id' => 'required|integer|exists:tbl_bereich,BereichID',
                'aktiv' => 'boolean'
            ]);

            $referenz->update([
                'Bezeichnung' => $validatedData['name'],
                'BereichID' => $validatedData['bereich_id'],
                'aktiv' => $validatedData['aktiv'] ?? $referenz->aktiv
            ]);

            return response()->json([
                'message' => 'Profile updated successfully', 
                'profile' => [
                    'id' => $referenz->ReferenzID,
                    'name' => $referenz->Bezeichnung,
                    'bereich_id' => $referenz->BereichID,
                    'aktiv' => $referenz->aktiv
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to update profile'], 500);
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
            $query = Referenz::with(['bereich.team.funktion', 'software', 'hardware', 'sammelrollen']);

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
                return [
                    'id' => $referenz->ReferenzID,
                    'name' => $referenz->Bezeichnung,
                    'bereich' => $referenz->bereich ? $referenz->bereich->Bezeichnung : 'Unbekannt',
                    'category' => $referenz->bereich && $referenz->bereich->team && $referenz->bereich->team->funktion 
                        ? $referenz->bereich->team->funktion->Bezeichnung : 'Allgemein',
                    'description' => 'Referenzprofil für ' . $referenz->Bezeichnung,
                    'hardwareCount' => $referenz->hardware->count(),
                    'softwareCount' => $referenz->software->count(),
                    'sapProfileCount' => $referenz->sammelrollen->count(),
                    'status' => $referenz->aktiv ? 'aktiv' : 'inaktiv',
                    'isTemplate' => true,
                    'created_at' => $referenz->created_at ? $referenz->created_at->format('Y-m-d') : now()->format('Y-m-d')
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