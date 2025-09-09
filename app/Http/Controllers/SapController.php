<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

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
     * Get all available SAP profile categories
     */
    public function getSapCategories(): JsonResponse
    {
        $categories = array_map(function($group) {
            return [
                'id' => $group['id'],
                'name' => $group['name'],
                'description' => $group['description'],
                'profile_count' => count($group['profiles'])
            ];
        }, $this->getSapProfileGroups());

        return response()->json([
            'categories' => $categories
        ]);
    }

    /**
     * SAP Profile Groups with detailed profiles
     */
    private function getSapProfileGroups(): array
    {
        return [
            [
                'id' => 1,
                'name' => 'Finanzwesen',
                'description' => 'Profile für Buchhaltung, Controlling und Finanzplanung',
                'profiles' => [
                    [
                        'id' => 101,
                        'name' => 'FI Buchhalter',
                        'code' => 'SAP_FI_BASIC',
                        'description' => 'Grundlegende Berechtigungen für Finanzbuchhaltung',
                        'permissions' => ['FB01', 'FB03', 'F-02', 'F-03', 'FS10N', 'FBL1N', 'FBL3N', 'F-43']
                    ],
                    [
                        'id' => 102,
                        'name' => 'CO Controlling',
                        'code' => 'SAP_CO_ANALYST',
                        'description' => 'Controlling und Kostenstellenauswertungen',
                        'permissions' => ['KS01', 'KS03', 'S_ALR_87013611', 'KSBT', 'KSH1', 'KSH2', 'KP06']
                    ],
                    [
                        'id' => 103,
                        'name' => 'FI/CO Manager',
                        'code' => 'SAP_FICO_MANAGER',
                        'description' => 'Erweiterte Berechtigungen für Finanz- und Controllingmanagement',
                        'permissions' => ['FB01', 'FB50', 'F-02', 'F-03', 'KS01', 'KS02', 'S_ALR_87013611', 'FAGLL03']
                    ],
                    [
                        'id' => 104,
                        'name' => 'Treasury Specialist',
                        'code' => 'SAP_TR_SPECIALIST',
                        'description' => 'Liquiditätsmanagement und Finanzinstrumente',
                        'permissions' => ['FF67', 'FF68', 'FF7A', 'S_ALR_87012172', 'TR01', 'TR02']
                    ]
                ]
            ],
            [
                'id' => 2,
                'name' => 'Vertrieb',
                'description' => 'Profile für Verkauf, Kundenbetreuung und Auftragsabwicklung',
                'profiles' => [
                    [
                        'id' => 201,
                        'name' => 'SD Verkauf',
                        'code' => 'SAP_SD_SALES',
                        'description' => 'Verkaufsbeleg-Erstellung und Kundenbetreuung',
                        'permissions' => ['VA01', 'VA02', 'VA03', 'VL01N', 'VF01', 'VF03', 'VKM1', 'VKM3']
                    ],
                    [
                        'id' => 202,
                        'name' => 'SD Manager',
                        'code' => 'SAP_SD_MANAGER',
                        'description' => 'Erweiterte Vertriebsberechtigungen mit Preispflege',
                        'permissions' => ['VA01', 'VA02', 'VA03', 'VK11', 'VK12', 'V/06', 'VF01', 'VF04', 'VKM1', 'VCCH']
                    ],
                    [
                        'id' => 203,
                        'name' => 'Kundenservice',
                        'code' => 'SAP_CS_AGENT',
                        'description' => 'Kundenbetreuung und Service-Management',
                        'permissions' => ['VA03', 'VF03', 'VL03N', 'VA05', 'VA05N', 'CRMD_ORDER', 'IW31', 'IW32']
                    ],
                    [
                        'id' => 204,
                        'name' => 'Pricing Analyst',
                        'code' => 'SAP_SD_PRICING',
                        'description' => 'Preisfindung und Konditionspflege',
                        'permissions' => ['VK11', 'VK12', 'VK13', 'V/06', 'V/07', 'MEK1', 'MEK2', 'MEKKO']
                    ]
                ]
            ],
            [
                'id' => 3,
                'name' => 'Einkauf & Logistik',
                'description' => 'Profile für Beschaffung, Lager und Materialwirtschaft',
                'profiles' => [
                    [
                        'id' => 301,
                        'name' => 'MM Einkauf',
                        'code' => 'SAP_MM_BUYER',
                        'description' => 'Bestellungen erstellen und Lieferanten verwalten',
                        'permissions' => ['ME21N', 'ME22N', 'ME23N', 'ME51N', 'ME52N', 'ME53N', 'ME2L', 'ME2M']
                    ],
                    [
                        'id' => 302,
                        'name' => 'WM Lager',
                        'code' => 'SAP_WM_OPERATOR',
                        'description' => 'Lagerbewegungen und Bestandsführung',
                        'permissions' => ['MIGO', 'MI01', 'MI02', 'LT01', 'LT03', 'LX02', 'LS24', 'LB01']
                    ],
                    [
                        'id' => 303,
                        'name' => 'MM Manager',
                        'code' => 'SAP_MM_MANAGER',
                        'description' => 'Materialwirtschaft Management und Strategischer Einkauf',
                        'permissions' => ['ME21N', 'ME31K', 'ME01', 'ME02', 'ME11', 'ME12', 'MK01', 'MK02']
                    ],
                    [
                        'id' => 304,
                        'name' => 'Inventory Specialist',
                        'code' => 'SAP_IM_SPECIALIST',
                        'description' => 'Bestandsmanagement und Inventur',
                        'permissions' => ['MI01', 'MI04', 'MI05', 'MI07', 'MI20', 'MI21', 'MMBE', 'MB1C']
                    ]
                ]
            ],
            [
                'id' => 4,
                'name' => 'Produktion',
                'description' => 'Profile für Fertigungsplanung und Produktionssteuerung',
                'profiles' => [
                    [
                        'id' => 401,
                        'name' => 'PP Planer',
                        'code' => 'SAP_PP_PLANNER',
                        'description' => 'Produktionsplanung und MRP-Steuerung',
                        'permissions' => ['MD01', 'MD02', 'MD03', 'MD04', 'CO01', 'CO02', 'CO03', 'CO11N']
                    ],
                    [
                        'id' => 402,
                        'name' => 'PP Shopfloor',
                        'code' => 'SAP_PP_SHOPFLOOR',
                        'description' => 'Fertigungsrückmeldung und Shopfloor-Steuerung',
                        'permissions' => ['CO11N', 'CO12', 'CO13', 'CO15', 'COOIS', 'CORT', 'MB1A', 'MB31']
                    ],
                    [
                        'id' => 403,
                        'name' => 'QM Quality Manager',
                        'code' => 'SAP_QM_MANAGER',
                        'description' => 'Qualitätsmanagement und Prüfplanung',
                        'permissions' => ['QP01', 'QP02', 'QA01', 'QA02', 'QE01', 'QE02', 'QM01', 'QM02']
                    ]
                ]
            ],
            [
                'id' => 5,
                'name' => 'Human Resources',
                'description' => 'Profile für Personalwesen und HR-Management',
                'profiles' => [
                    [
                        'id' => 501,
                        'name' => 'HR Administrator',
                        'code' => 'SAP_HR_ADMIN',
                        'description' => 'Personalverwaltung und Stammdatenpflege',
                        'permissions' => ['PA40', 'PA30', 'PA20', 'PU00', 'PU01', 'S_AHR_61016380', 'PC00_M10_CALC']
                    ],
                    [
                        'id' => 502,
                        'name' => 'Payroll Specialist',
                        'code' => 'SAP_PY_SPECIALIST',
                        'description' => 'Lohn- und Gehaltsabrechnung',
                        'permissions' => ['PC00_M10_CALC', 'PC00_M10_CIPE', 'H99_CUWB', 'PU03', 'PU12', 'S_AHR_61016380']
                    ],
                    [
                        'id' => 503,
                        'name' => 'HR Manager',
                        'code' => 'SAP_HR_MANAGER',
                        'description' => 'HR-Management und Personalentwicklung',
                        'permissions' => ['PA40', 'PA30', 'PHAP_CATALOG', 'PV01', 'PV02', 'S_AHR_61016380', 'PPOSE']
                    ]
                ]
            ],
            [
                'id' => 6,
                'name' => 'IT & Basis',
                'description' => 'Profile für Systemadministration und technische Betreuung',
                'profiles' => [
                    [
                        'id' => 601,
                        'name' => 'Basis Administrator',
                        'code' => 'SAP_BASIS_ADMIN',
                        'description' => 'SAP Basis Administration und Systembetreuung',
                        'permissions' => ['SM50', 'SM51', 'SM21', 'ST02', 'ST03', 'ST06', 'RZ10', 'RZ11']
                    ],
                    [
                        'id' => 602,
                        'name' => 'Security Administrator',
                        'code' => 'SAP_SEC_ADMIN',
                        'description' => 'Benutzer- und Berechtigungsverwaltung',
                        'permissions' => ['SU01', 'SU02', 'SU03', 'SU10', 'SU53', 'SUIM', 'PFCG', 'SU25']
                    ],
                    [
                        'id' => 603,
                        'name' => 'ABAP Developer',
                        'code' => 'SAP_DEV_ABAP',
                        'description' => 'ABAP-Entwicklung und Customizing',
                        'permissions' => ['SE80', 'SE38', 'SE11', 'SE16', 'SM30', 'SPRO', 'SCC4', 'SE01']
                    ]
                ]
            ]
        ];
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
     * Get profile statistics
     */
    public function getSapStatistics(): JsonResponse
    {
        $groups = $this->getSapProfileGroups();
        $totalProfiles = 0;
        $totalPermissions = 0;
        
        foreach ($groups as $group) {
            $totalProfiles += count($group['profiles']);
            foreach ($group['profiles'] as $profile) {
                $totalPermissions += count($profile['permissions']);
            }
        }

        return response()->json([
            'statistics' => [
                'total_groups' => count($groups),
                'total_profiles' => $totalProfiles,
                'total_permissions' => $totalPermissions,
                'groups_breakdown' => array_map(function($group) {
                    return [
                        'name' => $group['name'],
                        'profile_count' => count($group['profiles'])
                    ];
                }, $groups)
            ]
        ]);
    }
}