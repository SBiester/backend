<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\User;
use App\Models\Hardware;
use App\Models\Software;
use App\Models\Rollengruppe;
use App\Models\Sammelrollen;
use App\Models\Hersteller;
use App\Models\Referenz;
use App\Models\Auftrag;
use App\Models\Kategorie;

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
                'active' => User::count(), // Assuming all users are active for now
                'inactive' => 0
            ];

            // Hardware statistics
            $stats['hardware'] = [
                'total' => Hardware::count(),
                'categories' => Kategorie::count()
            ];

            // Software statistics
            $stats['software'] = [
                'total' => Software::count(),
                'manufacturers' => Hersteller::whereHas('software')->count()
            ];

            // SAP statistics
            $stats['sap'] = [
                'profiles' => Sammelrollen::count(),
                'groups' => Rollengruppe::count()
            ];

            // Profile statistics
            $stats['profiles'] = [
                'total' => Referenz::count(),
                'departments' => Referenz::distinct('BereichID')->count('BereichID')
            ];

            // Order statistics
            $stats['orders'] = [
                'active' => Auftrag::count(),
                'pending' => Auftrag::count() // All orders are pending for now
            ];

            return response()->json($stats);
        } catch (\Exception $e) {
            \Log::error('Dashboard stats error: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to load dashboard statistics: ' . $e->getMessage()], 500);
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
                      ->orWhereHas('kategorie', function($kq) use ($search) {
                          $kq->where('Bezeichnung', 'like', "%{$search}%");
                      });
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
            
            $hardware = $query->with('kategorie')->orderBy('Bezeichnung')
                ->paginate($perPage, ['*'], 'page', $page);

            $transformedData = $hardware->getCollection()->map(function($item) {
                return [
                    'id' => $item->HardwareID,
                    'name' => $item->Bezeichnung,
                    'manufacturer' => 'Hardware', // No manufacturer for hardware
                    'category' => $item->kategorie->Bezeichnung ?? 'Unbekannt',
                    'category_id' => $item->KategorieID,
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
                'category_id' => 'required|exists:tbl_kategorie,KategorieID'
            ]);

            $hardware = Hardware::create([
                'Bezeichnung' => $validatedData['name'],
                'KategorieID' => $validatedData['category_id']
            ]);

            return response()->json([
                'message' => 'Hardware item created successfully',
                'hardware' => [
                    'id' => $hardware->HardwareID,
                    'name' => $hardware->Bezeichnung,
                    'category_id' => $hardware->KategorieID
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
                'category_id' => 'required|exists:tbl_kategorie,KategorieID'
            ]);

            $hardware->update([
                'Bezeichnung' => $validatedData['name'],
                'KategorieID' => $validatedData['category_id']
            ]);

            return response()->json([
                'message' => 'Hardware item updated successfully',
                'hardware' => [
                    'id' => $hardware->HardwareID,
                    'name' => $hardware->Bezeichnung,
                    'category_id' => $hardware->KategorieID
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to update hardware item'], 500);
        }
    }

    /**
     * Get hardware categories
     */
    public function getHardwareCategories(): JsonResponse
    {
        try {
            $categories = Kategorie::withCount('hardware')->get();
            
            $categoriesData = $categories->map(function($kategorie) {
                return [
                    'id' => $kategorie->KategorieID,
                    'name' => $kategorie->Bezeichnung,
                    'description' => $kategorie->Bezeichnung,
                    'hardware_count' => $kategorie->hardware_count
                ];
            });
            
            return response()->json($categoriesData);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to load hardware categories'], 500);
        }
    }
    
    /**
     * Create hardware category
     */
    public function createHardwareCategory(Request $request): JsonResponse
    {
        try {
            $validatedData = $request->validate([
                'name' => 'required|string|max:255|unique:tbl_kategorie,Bezeichnung'
            ]);
            
            $category = Kategorie::create([
                'Bezeichnung' => $validatedData['name']
            ]);
            
            return response()->json([
                'message' => 'Hardware category created successfully',
                'category' => [
                    'id' => $category->KategorieID,
                    'name' => $category->Bezeichnung,
                    'description' => $category->Bezeichnung,
                    'hardware_count' => 0
                ]
            ], 201);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to create hardware category'], 500);
        }
    }
    
    /**
     * Update hardware category
     */
    public function updateHardwareCategory(Request $request, $id): JsonResponse
    {
        try {
            $category = Kategorie::where('KategorieID', $id)->first();
            if (!$category) {
                return response()->json(['error' => 'Hardware category not found'], 404);
            }
            
            $validatedData = $request->validate([
                'name' => 'required|string|max:255|unique:tbl_kategorie,Bezeichnung,' . $id . ',KategorieID'
            ]);
            
            $category->update([
                'Bezeichnung' => $validatedData['name']
            ]);
            
            return response()->json([
                'message' => 'Hardware category updated successfully', 
                'category' => [
                    'id' => $category->KategorieID,
                    'name' => $category->Bezeichnung,
                    'description' => $category->Bezeichnung,
                    'hardware_count' => $category->hardware()->count()
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to update hardware category'], 500);
        }
    }
    
    /**
     * Delete hardware category
     */
    public function deleteHardwareCategory($id): JsonResponse
    {
        try {
            $category = Kategorie::where('KategorieID', $id)->first();
            if (!$category) {
                return response()->json(['error' => 'Hardware category not found'], 404);
            }
            
            // Check if category has associated hardware
            if ($category->hardware()->count() > 0) {
                return response()->json(['error' => 'Cannot delete category with associated hardware'], 400);
            }
            
            $category->delete();
            
            return response()->json(['message' => 'Hardware category deleted successfully']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to delete hardware category'], 500);
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
            $query = Software::with(['hersteller']);

            // Apply search filter if provided
            if ($request->has('search') && !empty($request->search)) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('Bezeichnung', 'like', "%{$search}%")
                      ->orWhereHas('hersteller', function($hq) use ($search) {
                          $hq->where('Bezeichnung', 'like', "%{$search}%");
                      });
                });
            }

            // Apply manufacturer filter if provided
            if ($request->has('manufacturer') && !empty($request->manufacturer)) {
                $query->whereHas('hersteller', function($hq) use ($request) {
                    $hq->where('Bezeichnung', $request->manufacturer);
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
                    'manufacturer_id' => $item->HerstellerID,
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
            \Log::error('Software load error: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to load software items: ' . $e->getMessage()], 500);
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
                'manufacturer_id' => 'required|integer'
            ]);

            $software = Software::create([
                'Bezeichnung' => $validatedData['name'],
                'HerstellerID' => $validatedData['manufacturer_id'],
                'Sammelrollen' => false,
                'aktiv' => true
            ]);

            return response()->json([
                'message' => 'Software item created successfully',
                'software' => [
                    'id' => $software->SoftwareID,
                    'name' => $software->Bezeichnung
                ]
            ], 201);
        } catch (\Exception $e) {
            \Log::error('Software creation error: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to create software item: ' . $e->getMessage()], 500);
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
                'manufacturer_id' => 'required|integer'
            ]);

            $software->update([
                'Bezeichnung' => $validatedData['name'],
                'HerstellerID' => $validatedData['manufacturer_id']
            ]);

            return response()->json([
                'message' => 'Software item updated successfully',
                'software' => [
                    'id' => $software->SoftwareID,
                    'name' => $software->Bezeichnung
                ]
            ]);
        } catch (\Exception $e) {
            \Log::error('Software update error: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to update software item: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Get software manufacturers
     */
    public function getSoftwareManufacturers(): JsonResponse
    {
        try {
            $manufacturers = Hersteller::withCount('software')->get();
            
            $manufacturersData = $manufacturers->map(function($hersteller) {
                return [
                    'id' => $hersteller->HerstellerID,
                    'name' => $hersteller->Bezeichnung,
                    'description' => $hersteller->Bezeichnung,
                    'software_count' => $hersteller->software_count
                ];
            });
            
            return response()->json($manufacturersData);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to load software manufacturers'], 500);
        }
    }
    
    /**
     * Create software manufacturer
     */
    public function createSoftwareManufacturer(Request $request): JsonResponse
    {
        try {
            $validatedData = $request->validate([
                'name' => 'required|string|max:255|unique:tbl_hersteller,Bezeichnung'
            ]);
            
            $manufacturer = Hersteller::create([
                'Bezeichnung' => $validatedData['name']
            ]);
            
            return response()->json([
                'message' => 'Software manufacturer created successfully',
                'manufacturer' => [
                    'id' => $manufacturer->HerstellerID,
                    'name' => $manufacturer->Bezeichnung,
                    'description' => $manufacturer->Bezeichnung,
                    'software_count' => 0
                ]
            ], 201);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to create software manufacturer'], 500);
        }
    }
    
    /**
     * Update software manufacturer
     */
    public function updateSoftwareManufacturer(Request $request, $id): JsonResponse
    {
        try {
            $manufacturer = Hersteller::where('HerstellerID', $id)->first();
            if (!$manufacturer) {
                return response()->json(['error' => 'Software manufacturer not found'], 404);
            }
            
            $validatedData = $request->validate([
                'name' => 'required|string|max:255|unique:tbl_hersteller,Bezeichnung,' . $id . ',HerstellerID'
            ]);
            
            $manufacturer->update([
                'Bezeichnung' => $validatedData['name']
            ]);
            
            return response()->json([
                'message' => 'Software manufacturer updated successfully', 
                'manufacturer' => [
                    'id' => $manufacturer->HerstellerID,
                    'name' => $manufacturer->Bezeichnung,
                    'description' => $manufacturer->Bezeichnung,
                    'software_count' => $manufacturer->software()->count()
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to update software manufacturer'], 500);
        }
    }
    
    /**
     * Delete software manufacturer
     */
    public function deleteSoftwareManufacturer($id): JsonResponse
    {
        try {
            $manufacturer = Hersteller::where('HerstellerID', $id)->first();
            if (!$manufacturer) {
                return response()->json(['error' => 'Software manufacturer not found'], 404);
            }
            
            // Check if manufacturer has associated software
            if ($manufacturer->software()->count() > 0) {
                return response()->json(['error' => 'Cannot delete manufacturer with associated software'], 400);
            }
            
            $manufacturer->delete();
            
            return response()->json(['message' => 'Software manufacturer deleted successfully']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to delete software manufacturer'], 500);
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
                'RollengruppeID' => 'required|exists:tbl_rollengruppe,RollengruppeID'
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
                'RollengruppeID' => 'required|exists:tbl_rollengruppe,RollengruppeID'
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