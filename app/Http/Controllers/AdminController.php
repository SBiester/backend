<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\User;
use App\Models\Hardware;
use App\Models\Software;
use App\Models\Rollengruppe;
use App\Models\Sammelrollen;

class AdminController extends Controller
{
    /**
     * Get dashboard statistics
     */
    public function getDashboardStats(): JsonResponse
    {
        try {
            $stats = [];

            // Users statistics
            $stats['users'] = [
                'total' => User::count(),
                'active' => User::where('status', 'active')->count(),
                'inactive' => User::where('status', 'inactive')->count()
            ];

            // Hardware statistics
            $stats['hardware'] = [
                'total' => Hardware::count(),
                'categories' => Hardware::distinct('Hersteller')->count('Hersteller')
            ];

            // Software statistics
            $stats['software'] = [
                'total' => Software::count(),
                'manufacturers' => Software::distinct('Hersteller')->count('Hersteller')
            ];

            // SAP statistics
            $stats['sap'] = [
                'profiles' => Sammelrollen::count(),
                'groups' => Rollengruppe::count()
            ];

            return response()->json($stats);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to load dashboard statistics'], 500);
        }
    }

    /**
     * Get all users with pagination and search
     */
    public function getUsers(Request $request): JsonResponse
    {
        try {
            $query = User::query();

            if ($request->has('search') && !empty($request->search)) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('name', 'LIKE', "%{$search}%")
                      ->orWhere('email', 'LIKE', "%{$search}%")
                      ->orWhere('department', 'LIKE', "%{$search}%");
                });
            }

            if ($request->has('role') && !empty($request->role)) {
                $query->where('role', $request->role);
            }

            if ($request->has('status') && !empty($request->status)) {
                $query->where('status', $request->status);
            }

            $users = $query->paginate($request->get('per_page', 15));

            return response()->json($users);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to load users'], 500);
        }
    }

    /**
     * Create a new user
     */
    public function createUser(Request $request): JsonResponse
    {
        try {
            $validatedData = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email',
                'role' => 'required|in:admin,pm,fach,user',
                'department' => 'nullable|string|max:255',
                'status' => 'required|in:active,inactive'
            ]);

            $user = User::create($validatedData);

            return response()->json(['message' => 'User created successfully', 'user' => $user], 201);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to create user'], 500);
        }
    }

    /**
     * Update a user
     */
    public function updateUser(Request $request, $id): JsonResponse
    {
        try {
            $user = User::findOrFail($id);

            $validatedData = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email,' . $id,
                'role' => 'required|in:admin,pm,fach,user',
                'department' => 'nullable|string|max:255',
                'status' => 'required|in:active,inactive'
            ]);

            $user->update($validatedData);

            return response()->json(['message' => 'User updated successfully', 'user' => $user]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to update user'], 500);
        }
    }

    /**
     * Delete a user
     */
    public function deleteUser($id): JsonResponse
    {
        try {
            $user = User::findOrFail($id);
            $user->delete();

            return response()->json(['message' => 'User deleted successfully']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to delete user'], 500);
        }
    }

    /**
     * Get hardware items with search and filtering
     */
    public function getHardwareItems(Request $request): JsonResponse
    {
        try {
            $query = Hardware::query();

            // Apply search filter if provided
            if ($request->has('search') && !empty($request->search)) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('Bezeichnung', 'like', "%{$search}%")
                      ->orWhere('Hersteller', 'like', "%{$search}%");
                });
            }

            // Apply manufacturer filter if provided
            if ($request->has('manufacturer') && !empty($request->manufacturer)) {
                $query->where('Hersteller', $request->manufacturer);
            }

            // Pagination
            $page = $request->get('page', 1);
            $perPage = $request->get('per_page', 15);
            
            $hardware = $query->orderBy('Bezeichnung')
                ->paginate($perPage, ['*'], 'page', $page);

            $transformedData = $hardware->getCollection()->map(function($item) {
                return [
                    'id' => $item->HardwareID,
                    'name' => $item->Bezeichnung,
                    'manufacturer' => $item->Hersteller,
                    'category' => 'Hardware',
                    'description' => $item->Bezeichnung,
                    'created_at' => $item->created_at ? $item->created_at->format('Y-m-d') : now()->format('Y-m-d')
                ];
            });

            return response()->json([
                'data' => $transformedData,
                'current_page' => $hardware->currentPage(),
                'last_page' => $hardware->lastPage(),
                'per_page' => $hardware->perPage(),
                'total' => $hardware->total()
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to load hardware items'], 500);
        }
    }

    /**
     * Create hardware item
     */
    public function createHardwareItem(Request $request): JsonResponse
    {
        try {
            $validatedData = $request->validate([
                'name' => 'required|string|max:255',
                'manufacturer' => 'required|string|max:255'
            ]);

            $hardware = Hardware::create([
                'Bezeichnung' => $validatedData['name'],
                'Hersteller' => $validatedData['manufacturer']
            ]);

            return response()->json([
                'message' => 'Hardware item created successfully',
                'hardware' => [
                    'id' => $hardware->HardwareID,
                    'name' => $hardware->Bezeichnung,
                    'manufacturer' => $hardware->Hersteller
                ]
            ], 201);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to create hardware item'], 500);
        }
    }

    /**
     * Update hardware item
     */
    public function updateHardwareItem(Request $request, $id): JsonResponse
    {
        try {
            $hardware = Hardware::where('HardwareID', $id)->first();
            if (!$hardware) {
                return response()->json(['error' => 'Hardware item not found'], 404);
            }

            $validatedData = $request->validate([
                'name' => 'required|string|max:255',
                'manufacturer' => 'required|string|max:255'
            ]);

            $hardware->update([
                'Bezeichnung' => $validatedData['name'],
                'Hersteller' => $validatedData['manufacturer']
            ]);

            return response()->json([
                'message' => 'Hardware item updated successfully',
                'hardware' => [
                    'id' => $hardware->HardwareID,
                    'name' => $hardware->Bezeichnung,
                    'manufacturer' => $hardware->Hersteller
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to update hardware item'], 500);
        }
    }

    /**
     * Delete hardware item
     */
    public function deleteHardwareItem($id): JsonResponse
    {
        try {
            $hardware = Hardware::where('HardwareID', $id)->first();
            if (!$hardware) {
                return response()->json(['error' => 'Hardware item not found'], 404);
            }
            
            $hardware->delete();

            return response()->json(['message' => 'Hardware item deleted successfully']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to delete hardware item'], 500);
        }
    }

    /**
     * Get software items with search and filtering
     */
    public function getSoftwareItems(Request $request): JsonResponse
    {
        try {
            $query = Software::with(['hersteller', 'kategorie']);

            // Apply search filter if provided
            if ($request->has('search') && !empty($request->search)) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('Bezeichnung', 'like', "%{$search}%")
                      ->orWhere('Version', 'like', "%{$search}%")
                      ->orWhereHas('hersteller', function($hq) use ($search) {
                          $hq->where('Bezeichnung', 'like', "%{$search}%");
                      })
                      ->orWhereHas('kategorie', function($kq) use ($search) {
                          $kq->where('Bezeichnung', 'like', "%{$search}%");
                      });
                });
            }

            // Apply manufacturer filter if provided
            if ($request->has('manufacturer') && !empty($request->manufacturer)) {
                $query->whereHas('hersteller', function($hq) use ($request) {
                    $hq->where('Bezeichnung', $request->manufacturer);
                });
            }

            // Apply category filter if provided  
            if ($request->has('category') && !empty($request->category)) {
                $query->whereHas('kategorie', function($kq) use ($request) {
                    $kq->where('Bezeichnung', $request->category);
                });
            }

            // Pagination
            $page = $request->get('page', 1);
            $perPage = $request->get('per_page', 15);
            
            $software = $query->orderBy('Bezeichnung')
                ->paginate($perPage, ['*'], 'page', $page);

            $transformedData = $software->getCollection()->map(function($item) {
                return [
                    'id' => $item->SoftwareID,
                    'name' => $item->Bezeichnung,
                    'manufacturer' => $item->hersteller->Bezeichnung ?? 'Unbekannt',
                    'category' => $item->kategorie->Bezeichnung ?? 'Unbekannt',
                    'version' => $item->Version,
                    'license_type' => $item->Lizenztyp ?? 'Unbekannt',
                    'created_at' => $item->created_at ? $item->created_at->format('Y-m-d') : now()->format('Y-m-d')
                ];
            });

            return response()->json([
                'data' => $transformedData,
                'current_page' => $software->currentPage(),
                'last_page' => $software->lastPage(),
                'per_page' => $software->perPage(),
                'total' => $software->total()
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to load software items'], 500);
        }
    }

    /**
     * Create software item
     */
    public function createSoftwareItem(Request $request): JsonResponse
    {
        try {
            $validatedData = $request->validate([
                'name' => 'required|string|max:255',
                'manufacturer_id' => 'required|integer',
                'category_id' => 'required|integer',
                'version' => 'nullable|string|max:50',
                'license_type' => 'nullable|string|max:100'
            ]);

            $software = Software::create([
                'Bezeichnung' => $validatedData['name'],
                'HerstellerID' => $validatedData['manufacturer_id'],
                'KategorieID' => $validatedData['category_id'],
                'Version' => $validatedData['version'] ?? '',
                'Lizenztyp' => $validatedData['license_type'] ?? ''
            ]);

            return response()->json([
                'message' => 'Software item created successfully',
                'software' => [
                    'id' => $software->SoftwareID,
                    'name' => $software->Bezeichnung,
                    'version' => $software->Version,
                    'license_type' => $software->Lizenztyp
                ]
            ], 201);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to create software item'], 500);
        }
    }

    /**
     * Update software item
     */
    public function updateSoftwareItem(Request $request, $id): JsonResponse
    {
        try {
            $software = Software::where('SoftwareID', $id)->first();
            if (!$software) {
                return response()->json(['error' => 'Software item not found'], 404);
            }

            $validatedData = $request->validate([
                'name' => 'required|string|max:255',
                'manufacturer_id' => 'required|integer',
                'category_id' => 'required|integer',
                'version' => 'nullable|string|max:50',
                'license_type' => 'nullable|string|max:100'
            ]);

            $software->update([
                'Bezeichnung' => $validatedData['name'],
                'HerstellerID' => $validatedData['manufacturer_id'],
                'KategorieID' => $validatedData['category_id'],
                'Version' => $validatedData['version'] ?? '',
                'Lizenztyp' => $validatedData['license_type'] ?? ''
            ]);

            return response()->json([
                'message' => 'Software item updated successfully',
                'software' => [
                    'id' => $software->SoftwareID,
                    'name' => $software->Bezeichnung,
                    'version' => $software->Version,
                    'license_type' => $software->Lizenztyp
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to update software item'], 500);
        }
    }

    /**
     * Delete software item
     */
    public function deleteSoftwareItem($id): JsonResponse
    {
        try {
            $software = Software::where('SoftwareID', $id)->first();
            if (!$software) {
                return response()->json(['error' => 'Software item not found'], 404);
            }
            
            $software->delete();

            return response()->json(['message' => 'Software item deleted successfully']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to delete software item'], 500);
        }
    }

    /**
     * Get SAP roles and groups
     */
    public function getSapRoles(Request $request): JsonResponse
    {
        try {
            $query = Sammelrollen::with('rollengruppe');

            if ($request->has('search') && !empty($request->search)) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('Bezeichnung', 'LIKE', "%{$search}%")
                      ->orWhere('Schluessel', 'LIKE', "%{$search}%")
                      ->orWhereHas('rollengruppe', function($rq) use ($search) {
                          $rq->where('Bezeichnung', 'LIKE', "%{$search}%");
                      });
                });
            }

            if ($request->has('group') && !empty($request->group)) {
                $query->where('RollengruppeID', $request->group);
            }

            $roles = $query->paginate($request->get('per_page', 20));

            return response()->json($roles);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to load SAP roles'], 500);
        }
    }

    /**
     * Get SAP role groups
     */
    public function getSapRoleGroups(): JsonResponse
    {
        try {
            $groups = Rollengruppe::with('sammelrollen')->get();
            return response()->json($groups);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to load SAP role groups'], 500);
        }
    }

    /**
     * Create SAP role
     */
    public function createSapRole(Request $request): JsonResponse
    {
        try {
            $validatedData = $request->validate([
                'Bezeichnung' => 'required|string|max:255',
                'Schluessel' => 'nullable|string|max:100',
                'RollengruppeID' => 'required|exists:Rollengruppe,RollengruppeID'
            ]);

            $role = Sammelrollen::create($validatedData);
            $role->load('rollengruppe');

            return response()->json(['message' => 'SAP role created successfully', 'role' => $role], 201);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to create SAP role'], 500);
        }
    }

    /**
     * Update SAP role
     */
    public function updateSapRole(Request $request, $id): JsonResponse
    {
        try {
            $role = Sammelrollen::findOrFail($id);

            $validatedData = $request->validate([
                'Bezeichnung' => 'required|string|max:255',
                'Schluessel' => 'nullable|string|max:100',
                'RollengruppeID' => 'required|exists:Rollengruppe,RollengruppeID'
            ]);

            $role->update($validatedData);
            $role->load('rollengruppe');

            return response()->json(['message' => 'SAP role updated successfully', 'role' => $role]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to update SAP role'], 500);
        }
    }

    /**
     * Delete SAP role
     */
    public function deleteSapRole($id): JsonResponse
    {
        try {
            $role = Sammelrollen::findOrFail($id);
            $role->delete();

            return response()->json(['message' => 'SAP role deleted successfully']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to delete SAP role'], 500);
        }
    }

    /**
     * Create SAP role group
     */
    public function createSapRoleGroup(Request $request): JsonResponse
    {
        try {
            $validatedData = $request->validate([
                'Bezeichnung' => 'required|string|max:255'
            ]);

            $group = Rollengruppe::create($validatedData);

            return response()->json(['message' => 'SAP role group created successfully', 'group' => $group], 201);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to create SAP role group'], 500);
        }
    }

    /**
     * Update SAP role group
     */
    public function updateSapRoleGroup(Request $request, $id): JsonResponse
    {
        try {
            $group = Rollengruppe::findOrFail($id);

            $validatedData = $request->validate([
                'Bezeichnung' => 'required|string|max:255'
            ]);

            $group->update($validatedData);

            return response()->json(['message' => 'SAP role group updated successfully', 'group' => $group]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to update SAP role group'], 500);
        }
    }

    /**
     * Delete SAP role group
     */
    public function deleteSapRoleGroup($id): JsonResponse
    {
        try {
            $group = Rollengruppe::findOrFail($id);
            
            // Check if group has associated roles
            if ($group->sammelrollen()->count() > 0) {
                return response()->json(['error' => 'Cannot delete group with associated roles'], 400);
            }

            $group->delete();

            return response()->json(['message' => 'SAP role group deleted successfully']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to delete SAP role group'], 500);
        }
    }
}