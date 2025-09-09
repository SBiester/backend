<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class JobUpdateController extends Controller
{
    /**
     * Job-Update empfangen und validieren
     */
    public function submitJobUpdate(Request $request): JsonResponse
    {
        try {
            // Validate the incoming request
            $validator = Validator::make($request->all(), [
                'vorname' => 'required|string|max:255',
                'nachname' => 'required|string|max:255',
                'employerType' => 'required|boolean',
                'updateType' => 'required|string|in:Eintritt,Verlängerung,Wechsel,Änderung,Austritt',
                'itUserName' => 'nullable|string|max:255',
                'bereich' => 'nullable|string|max:255',
                'sachbereich' => 'nullable|string|max:255',
                'funktion' => 'nullable|string|max:255',
                'position' => 'nullable|string|max:255',
                'vorgesetzt' => 'nullable|string|max:255',
                'eintritt' => 'nullable|date',
                'frist' => 'nullable|date',
                'refprofil' => 'nullable|array',
                'refprofil.*.id' => 'required|integer',
                'refprofil.*.name' => 'required|string',
                'additionalHardware' => 'nullable|array',
                'additionalHardware.*.id' => 'required|integer',
                'additionalHardware.*.name' => 'required|string',
                'additionalSoftware' => 'nullable|array',
                'additionalSoftware.*.id' => 'required|integer',
                'additionalSoftware.*.name' => 'required|string',
                'sapProfiles' => 'nullable|array',
                'sapProfiles.*.id' => 'required|integer',
                'sapProfiles.*.name' => 'required|string',
                'sapProfiles.*.code' => 'required|string',
                'selectedSap' => 'nullable|boolean',
                'additionalOptions' => 'nullable|array',
                'additionalOptions.telefonnummer' => 'nullable|boolean',
                'additionalOptions.tuerschild' => 'nullable|boolean',
                'additionalOptions.visitenkarten' => 'nullable|boolean',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validierungsfehler',
                    'errors' => $validator->errors()
                ], 422);
            }

            $data = $validator->validated();

            Log::info('Job Update eingegangen:', $data);

            // Nach Beendigung der Admin View hier Verarbeitung der Daten einfügen

            $processedData = $this->processJobUpdate($data);

            return response()->json([
                'success' => true,
                'message' => 'Job-Update erfolgreich übermittelt',
                'data' => $processedData,
                'ticket_id' => 'JU-' . time() // derzeit Fake-Ticket-ID später durch echte ID ersetzen
            ], 200);

        } catch (\Exception $e) {
            Log::error('Fehler beim Verarbeiten des Job-Updates:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Fehler beim Verarbeiten des Job-Updates'
            ], 500);
        }
    }

    /**
     * Daten werden ausgewertet
     */
    private function processJobUpdate(array $data): array {
        $processed = [
            'mitarbeiter' => [
                'name' => $data['vorname'] . ' ' . $data['nachname'],
                'typ' => $data['employerType'] ? 'Intern' : 'Extern',
                'update_typ' => $data['updateType'],
                'it_username' => $data['itUserName'] ?? null,
                'bereich' => $data['bereich'] ?? null,
                'sachbereich' => $data['sachbereich'] ?? null,
                'funktion' => $data['funktion'] ?? null,
                'position' => $data['position'] ?? null,
                'vorgesetzt' => $data['vorgesetzt'] ?? null,
                'eintritt' => $data['eintritt'] ?? null,
                'frist' => $data['frist'] ?? null,
            ],
            'referenzprofile' => $data['refprofil'] ?? [],
            'hardware' => $data['additionalHardware'] ?? [],
            'software' => $data['additionalSoftware'] ?? [],
            'sap_profiles' => $data['sapProfiles'] ?? [],
            'services' => []
        ];

        if (!empty($data['additionalOptions'])) {
            $options = $data['additionalOptions'];
            if ($options['telefonnummer'] ?? false) {
                $processed['services'][] = 'Telefonnummer';
            }
            if ($options['tuerschild'] ?? false) {
                $processed['services'][] = 'Türschild';
            }
            if ($options['visitenkarten'] ?? false) {
                $processed['services'][] = 'Visitenkarten';
            }
        }

        $processed['zusammenfassung'] = [
            'referenzprofile_anzahl' => count($processed['referenzprofile']),
            'hardware_anzahl' => count($processed['hardware']),
            'software_anzahl' => count($processed['software']),
            'sap_profile_anzahl' => count($processed['sap_profiles']),
            'services_anzahl' => count($processed['services']),
        ];

        return $processed;
    }
}