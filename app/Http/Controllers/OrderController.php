<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use App\Models\Auftrag;
use App\Models\Status;
use App\Models\VeraenderungArt;

class OrderController extends Controller
{
    /**
     * Get all job-update orders with optional filtering
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $query = Auftrag::with(['mitarbeiter', 'status', 'veraenderung.veraenderungArt']);
            
            // Apply filters
            if ($request->has('search') && $request->search) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->whereHas('mitarbeiter', function($mq) use ($search) {
                        $mq->where('Vorname', 'like', "%{$search}%")
                          ->orWhere('Name', 'like', "%{$search}%");
                    })->orWhere('AuftragID', 'like', "%{$search}%")
                      ->orWhere('Kommentar', 'like', "%{$search}%");
                });
            }
            
            if ($request->has('status') && $request->status) {
                $query->whereHas('status', function($sq) use ($request) {
                    $sq->where('Bezeichnung', $request->status);
                });
            }
            
            if ($request->has('type') && $request->type) {
                $query->whereHas('veraenderung.veraenderungArt', function($vq) use ($request) {
                    $vq->where('Bezeichnung', $request->type);
                });
            }
            
            // Pagination
            $page = $request->get('page', 1);
            $perPage = $request->get('per_page', 20);
            
            $orders = $query->paginate($perPage, ['*'], 'page', $page);
            
            // Transform data to match frontend expectations
            $transformedData = $orders->getCollection()->map(function ($auftrag) {
                return [
                    'id' => $auftrag->AuftragID,
                    'employee_name' => $auftrag->mitarbeiter ? $auftrag->mitarbeiter->full_name : 'Unbekannt',
                    'employee_email' => $auftrag->mitarbeiter ? strtolower($auftrag->mitarbeiter->Vorname . '.' . $auftrag->mitarbeiter->Name . '@company.com') : null,
                    'department' => $auftrag->mitarbeiter && $auftrag->mitarbeiter->bereich ? $auftrag->mitarbeiter->bereich->Bezeichnung : 'Unbekannt',
                    'type' => $auftrag->veraenderung && $auftrag->veraenderung->veraenderungArt ? $auftrag->veraenderung->veraenderungArt->Bezeichnung : 'Unbekannt',
                    'status' => $auftrag->status ? strtolower(str_replace(' ', '_', $auftrag->status->Bezeichnung)) : 'pending',
                    'created_at' => $auftrag->AuftragDatum ? $auftrag->AuftragDatum->toISOString() : now()->toISOString(),
                    'updated_at' => $auftrag->AuftragDatum ? $auftrag->AuftragDatum->toISOString() : now()->toISOString(),
                    'notes' => $auftrag->Kommentar,
                    'auftrag_ma' => $auftrag->AuftragMA
                ];
            });
            
            return response()->json([
                'data' => $transformedData,
                'current_page' => $orders->currentPage(),
                'per_page' => $orders->perPage(),
                'total' => $orders->total(),
                'last_page' => $orders->lastPage(),
                'from' => $orders->firstItem(),
                'to' => $orders->lastItem()
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error fetching orders: ' . $e->getMessage());
            
            return response()->json([
                'error' => 'Failed to fetch orders',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get orders for the current authenticated user
     */
    public function getUserOrders(Request $request): JsonResponse
    {
        try {
            // Get user from authentication or use mock
            $user = $request->user();
            $userEmail = $user ? $user->email : 'max.mustermann@company.com';
            
            // Extract username from email for matching against employee names
            $username = explode('@', $userEmail)[0];
            $nameParts = explode('.', $username);
            
            $query = Auftrag::with(['mitarbeiter', 'status', 'veraenderung.veraenderungArt'])
                ->whereHas('mitarbeiter', function($mq) use ($nameParts) {
                    if (count($nameParts) >= 2) {
                        $mq->where('Vorname', 'like', '%' . $nameParts[0] . '%')
                          ->where('Name', 'like', '%' . $nameParts[1] . '%');
                    } else {
                        $mq->where('Vorname', 'like', '%' . $nameParts[0] . '%')
                          ->orWhere('Name', 'like', '%' . $nameParts[0] . '%');
                    }
                })
                ->open(); // Only open orders
            
            $orders = $query->get();
            
            // Transform data to match frontend expectations
            $transformedData = $orders->map(function ($auftrag) {
                return [
                    'id' => $auftrag->AuftragID,
                    'employee_name' => $auftrag->mitarbeiter ? $auftrag->mitarbeiter->full_name : 'Unbekannt',
                    'employee_email' => $auftrag->mitarbeiter ? strtolower($auftrag->mitarbeiter->Vorname . '.' . $auftrag->mitarbeiter->Name . '@company.com') : null,
                    'department' => $auftrag->mitarbeiter && $auftrag->mitarbeiter->bereich ? $auftrag->mitarbeiter->bereich->Bezeichnung : 'Unbekannt',
                    'type' => $auftrag->veraenderung && $auftrag->veraenderung->veraenderungArt ? $auftrag->veraenderung->veraenderungArt->Bezeichnung : 'Unbekannt',
                    'status' => $auftrag->status ? strtolower(str_replace(' ', '_', $auftrag->status->Bezeichnung)) : 'pending',
                    'created_at' => $auftrag->AuftragDatum ? $auftrag->AuftragDatum->toISOString() : now()->toISOString(),
                    'updated_at' => $auftrag->AuftragDatum ? $auftrag->AuftragDatum->toISOString() : now()->toISOString(),
                    'notes' => $auftrag->Kommentar,
                    'auftrag_ma' => $auftrag->AuftragMA
                ];
            });
            
            return response()->json($transformedData);
            
        } catch (\Exception $e) {
            Log::error('Error fetching user orders: ' . $e->getMessage());
            
            return response()->json([
                'error' => 'Failed to fetch user orders',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get single order by ID
     */
    public function show(Request $request, $id): JsonResponse
    {
        try {
            $auftrag = Auftrag::with(['mitarbeiter.bereich', 'status', 'veraenderung.veraenderungArt', 'referenzen', 'elemente.software', 'elemente.hardware', 'elemente.sammelrollen'])
                ->find($id);
            
            if (!$auftrag) {
                return response()->json([
                    'error' => 'Order not found'
                ], 404);
            }
            
            $transformedOrder = [
                'id' => $auftrag->AuftragID,
                'employee_name' => $auftrag->mitarbeiter ? $auftrag->mitarbeiter->full_name : 'Unbekannt',
                'employee_email' => $auftrag->mitarbeiter ? strtolower($auftrag->mitarbeiter->Vorname . '.' . $auftrag->mitarbeiter->Name . '@company.com') : null,
                'department' => $auftrag->mitarbeiter && $auftrag->mitarbeiter->bereich ? $auftrag->mitarbeiter->bereich->Bezeichnung : 'Unbekannt',
                'type' => $auftrag->veraenderung && $auftrag->veraenderung->veraenderungArt ? $auftrag->veraenderung->veraenderungArt->Bezeichnung : 'Unbekannt',
                'status' => $auftrag->status ? strtolower(str_replace(' ', '_', $auftrag->status->Bezeichnung)) : 'pending',
                'created_at' => $auftrag->AuftragDatum ? $auftrag->AuftragDatum->toISOString() : now()->toISOString(),
                'updated_at' => $auftrag->AuftragDatum ? $auftrag->AuftragDatum->toISOString() : now()->toISOString(),
                'notes' => $auftrag->Kommentar,
                'auftrag_ma' => $auftrag->AuftragMA,
                'referenzen' => $auftrag->referenzen->map(function($ref) {
                    return [
                        'id' => $ref->ReferenzID,
                        'bezeichnung' => $ref->Bezeichnung
                    ];
                }),
                'elemente' => $auftrag->elemente->map(function($element) {
                    return [
                        'id' => $element->ElementID,
                        'bezeichnung' => $element->Bezeichnung,
                        'type' => $element->type,
                        'item' => $element->item ? [
                            'id' => $element->item->getKey(),
                            'bezeichnung' => $element->item->Bezeichnung ?? $element->item->Name ?? 'Unbekannt'
                        ] : null
                    ];
                })
            ];
            
            return response()->json([
                'data' => $transformedOrder
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error fetching order: ' . $e->getMessage());
            
            return response()->json([
                'error' => 'Failed to fetch order',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update order status
     */
    public function updateStatus(Request $request, $id): JsonResponse
    {
        $request->validate([
            'status' => 'required|string'
        ]);
        
        try {
            $auftrag = Auftrag::find($id);
            
            if (!$auftrag) {
                return response()->json([
                    'error' => 'Order not found'
                ], 404);
            }
            
            // Find or create status
            $status = Status::where('Bezeichnung', $request->status)->first();
            if (!$status) {
                $status = Status::create(['Bezeichnung' => $request->status]);
            }
            
            $auftrag->StatusID = $status->StatusID;
            $auftrag->save();
            
            return response()->json([
                'message' => 'Order status updated successfully',
                'data' => [
                    'id' => $auftrag->AuftragID,
                    'status' => strtolower(str_replace(' ', '_', $status->Bezeichnung)),
                    'updated_at' => now()->toISOString()
                ]
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error updating order status: ' . $e->getMessage());
            
            return response()->json([
                'error' => 'Failed to update order status',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Process order (complete job-update)
     */
    public function processOrder(Request $request, $id): JsonResponse
    {
        $request->validate([
            'processed_by' => 'required|string',
            'notes' => 'nullable|string'
        ]);
        
        try {
            // In a real implementation, this would:
            // 1. Update the employee's data according to the job-update
            // 2. Mark the order as completed
            // 3. Log the processing details
            
            return response()->json([
                'message' => 'Order processed successfully',
                'data' => [
                    'id' => (int) $id,
                    'status' => 'completed',
                    'processed_by' => $request->processed_by,
                    'processed_at' => now()->toISOString(),
                    'notes' => $request->notes
                ]
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error processing order: ' . $e->getMessage());
            
            return response()->json([
                'error' => 'Failed to process order',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get order statistics
     */
    public function getStats(): JsonResponse
    {
        try {
            $totalOrders = Auftrag::count();
            $statusStats = Auftrag::select('tbl_status.Bezeichnung as status', DB::raw('count(*) as count'))
                ->join('tbl_status', 'tbl_auftrag.StatusID', '=', 'tbl_status.StatusID')
                ->groupBy('tbl_status.Bezeichnung')
                ->pluck('count', 'status');
            
            $todayOrders = Auftrag::whereDate('AuftragDatum', today())->count();
            $weekOrders = Auftrag::whereBetween('AuftragDatum', [now()->startOfWeek(), now()->endOfWeek()])->count();
            
            $typeStats = Auftrag::select('tbl_veraenderung_art.Bezeichnung as type', DB::raw('count(*) as count'))
                ->join('tbl_veraenderung', 'tbl_auftrag.VeraenderungID', '=', 'tbl_veraenderung.VeraenderungID')
                ->join('tbl_veraenderung_art', 'tbl_veraenderung.Veraenderung_ArtID', '=', 'tbl_veraenderung_art.Veraenderung_ArtID')
                ->groupBy('tbl_veraenderung_art.Bezeichnung')
                ->pluck('count', 'type');
            
            $stats = [
                'total' => $totalOrders,
                'pending' => $statusStats['pending'] ?? $statusStats['ausstehend'] ?? 0,
                'in_progress' => $statusStats['in_progress'] ?? $statusStats['in bearbeitung'] ?? 0,
                'completed' => $statusStats['completed'] ?? $statusStats['abgeschlossen'] ?? 0,
                'cancelled' => $statusStats['cancelled'] ?? $statusStats['abgebrochen'] ?? 0,
                'today' => $todayOrders,
                'this_week' => $weekOrders,
                'by_type' => $typeStats->toArray()
            ];
            
            return response()->json([
                'data' => $stats
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error fetching order stats: ' . $e->getMessage());
            
            return response()->json([
                'error' => 'Failed to fetch order statistics',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Export orders to CSV
     */
    public function exportOrders(Request $request): JsonResponse
    {
        try {
            $format = $request->get('format', 'csv');
            
            // Build query with same filters as index method
            $query = Auftrag::with(['mitarbeiter', 'status', 'veraenderung.veraenderungArt']);
            
            // Apply filters
            if ($request->has('search') && $request->search) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->whereHas('mitarbeiter', function($mq) use ($search) {
                        $mq->where('Vorname', 'like', "%{$search}%")
                          ->orWhere('Name', 'like', "%{$search}%");
                    })->orWhere('AuftragID', 'like', "%{$search}%")
                      ->orWhere('Kommentar', 'like', "%{$search}%");
                });
            }
            
            if ($request->has('status') && $request->status) {
                $query->whereHas('status', function($sq) use ($request) {
                    $sq->where('Bezeichnung', $request->status);
                });
            }
            
            if ($request->has('type') && $request->type) {
                $query->whereHas('veraenderung.veraenderungArt', function($vq) use ($request) {
                    $vq->where('Bezeichnung', $request->type);
                });
            }
            
            $count = $query->count();
            
            // For now, just return success message
            // In real implementation, generate and return the file
            
            return response()->json([
                'message' => 'Export started',
                'count' => $count,
                'format' => $format
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error exporting orders: ' . $e->getMessage());
            
            return response()->json([
                'error' => 'Failed to export orders',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}