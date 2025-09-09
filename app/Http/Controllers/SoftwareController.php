<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

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

        // Beispieldaten für Software basierend auf Referenzprofilen
        $softwareData = $this->getSoftwareMapping();
        
        if (!isset($softwareData[$profileName])) {
            return response()->json(['error' => 'Profile not found'], 404);
        }

        return response()->json([
            'profile' => $profileName,
            'software' => $softwareData[$profileName]
        ]);
    }

    /**
     * Get all available software items
     */
    public function getAllSoftware(): JsonResponse
    {
        return response()->json([
            'software' => $this->getAllSoftwareItems()
        ]);
    }

    /**
     * Get additional software items for selection
     */
    public function getAdditionalSoftware(): JsonResponse
    {
        return response()->json([
            'software' => $this->getAdditionalSoftwareItems()
        ]);
    }

    /**
     * Get software manufacturers
     */
    public function getManufacturers(): JsonResponse
    {
        return response()->json([
            'manufacturers' => $this->getSoftwareManufacturers()
        ]);
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

        $softwareByManufacturer = $this->getSoftwareByManufacturerData();
        
        if (!isset($softwareByManufacturer[$manufacturer])) {
            return response()->json(['error' => 'Manufacturer not found'], 404);
        }

        return response()->json([
            'manufacturer' => $manufacturer,
            'software' => $softwareByManufacturer[$manufacturer]
        ]);
    }

    /**
     * Software mapping for reference profiles
     */
    private function getSoftwareMapping(): array
    {
        return [
            'Böhm Finn' => [
                [
                    'id' => 1,
                    'name' => 'Microsoft Office 365',
                    'category' => 'Büro',
                    'description' => 'Vollständige Office-Suite mit Word, Excel, PowerPoint',
                    'version' => '2023'
                ],
                [
                    'id' => 2,
                    'name' => 'Adobe Creative Suite',
                    'category' => 'Design',
                    'description' => 'Photoshop, Illustrator, InDesign für professionelle Gestaltung',
                    'version' => '2024'
                ],
                [
                    'id' => 3,
                    'name' => 'Visual Studio Code',
                    'category' => 'Entwicklung',
                    'description' => 'Moderner Code-Editor für verschiedene Programmiersprachen',
                    'version' => '1.85'
                ],
                [
                    'id' => 4,
                    'name' => 'Slack',
                    'category' => 'Kommunikation',
                    'description' => 'Team-Kommunikation und Collaboration-Tool',
                    'version' => '4.37'
                ],
                [
                    'id' => 5,
                    'name' => 'Zoom',
                    'category' => 'Videokonferenz',
                    'description' => 'Video-Konferenz und Meeting-Software',
                    'version' => '5.17'
                ]
            ],
            'Wirschinin Elena' => [
                [
                    'id' => 6,
                    'name' => 'Final Cut Pro',
                    'category' => 'Video',
                    'description' => 'Professionelle Videobearbeitung für macOS',
                    'version' => '10.7'
                ],
                [
                    'id' => 7,
                    'name' => 'Logic Pro',
                    'category' => 'Audio',
                    'description' => 'Professionelle Musik-Produktion und Audio-Bearbeitung',
                    'version' => '10.8'
                ],
                [
                    'id' => 8,
                    'name' => 'Sketch',
                    'category' => 'Design',
                    'description' => 'UI/UX Design Tool für macOS',
                    'version' => '99'
                ],
                [
                    'id' => 9,
                    'name' => 'Xcode',
                    'category' => 'Entwicklung',
                    'description' => 'Apple Entwicklungsumgebung für iOS und macOS Apps',
                    'version' => '15.2'
                ],
                [
                    'id' => 10,
                    'name' => 'Safari',
                    'category' => 'Browser',
                    'description' => 'Standard-Webbrowser für macOS',
                    'version' => '17.2'
                ]
            ],
            'Breidenbicher Kjell' => [
                [
                    'id' => 11,
                    'name' => 'IntelliJ IDEA',
                    'category' => 'Entwicklung',
                    'description' => 'Professionelle Java-Entwicklungsumgebung',
                    'version' => '2023.3'
                ],
                [
                    'id' => 12,
                    'name' => 'Docker Desktop',
                    'category' => 'Entwicklung',
                    'description' => 'Container-Virtualisierung für Entwicklung',
                    'version' => '4.26'
                ],
                [
                    'id' => 13,
                    'name' => 'Postman',
                    'category' => 'Entwicklung',
                    'description' => 'API-Testing und Dokumentation',
                    'version' => '10.22'
                ],
                [
                    'id' => 14,
                    'name' => 'Microsoft Teams',
                    'category' => 'Kommunikation',
                    'description' => 'Team-Zusammenarbeit und Video-Konferenzen',
                    'version' => '1.6'
                ],
                [
                    'id' => 15,
                    'name' => 'Git',
                    'category' => 'Entwicklung',
                    'description' => 'Versionskontrolle für Code-Projekte',
                    'version' => '2.43'
                ]
            ],
            'Saed Abdo' => [
                [
                    'id' => 16,
                    'name' => 'LibreOffice',
                    'category' => 'Büro',
                    'description' => 'Open Source Office-Suite',
                    'version' => '7.6'
                ],
                [
                    'id' => 17,
                    'name' => 'Firefox',
                    'category' => 'Browser',
                    'description' => 'Open Source Webbrowser',
                    'version' => '121'
                ],
                [
                    'id' => 18,
                    'name' => 'Thunderbird',
                    'category' => 'E-Mail',
                    'description' => 'E-Mail-Client und Kalender',
                    'version' => '115'
                ],
                [
                    'id' => 19,
                    'name' => 'VLC Media Player',
                    'category' => 'Multimedia',
                    'description' => 'Universeller Mediaplayer',
                    'version' => '3.0.20'
                ],
                [
                    'id' => 20,
                    'name' => 'Notepad++',
                    'category' => 'Editor',
                    'description' => 'Erweiteter Texteditor für Programmierung',
                    'version' => '8.6'
                ]
            ]
        ];
    }

    /**
     * Get all available software items
     */
    private function getAllSoftwareItems(): array
    {
        $allSoftware = [];
        foreach ($this->getSoftwareMapping() as $profile => $software) {
            $allSoftware = array_merge($allSoftware, $software);
        }
        return $allSoftware;
    }

    /**
     * Get additional software items for selection
     */
    private function getAdditionalSoftwareItems(): array
    {
        return [
            [
                'id' => 101,
                'name' => 'Figma',
                'category' => 'Design',
                'description' => 'Kollaboratives UI/UX Design Tool',
                'version' => '116.16'
            ],
            [
                'id' => 102,
                'name' => 'Notion',
                'category' => 'Produktivität',
                'description' => 'All-in-One Workspace für Notizen und Projektmanagement',
                'version' => '3.5'
            ],
            [
                'id' => 103,
                'name' => 'Spotify',
                'category' => 'Multimedia',
                'description' => 'Musik-Streaming Anwendung',
                'version' => '1.2.26'
            ],
            [
                'id' => 104,
                'name' => 'Blender',
                'category' => 'Design',
                'description' => '3D-Modellierung und Animation Software',
                'version' => '4.0'
            ],
            [
                'id' => 105,
                'name' => 'OBS Studio',
                'category' => 'Multimedia',
                'description' => 'Open Source Software für Video-Aufnahme und Live-Streaming',
                'version' => '30.0'
            ],
            [
                'id' => 106,
                'name' => 'Discord',
                'category' => 'Kommunikation',
                'description' => 'Voice und Text Chat für Communities',
                'version' => '1.0.9150'
            ],
            [
                'id' => 107,
                'name' => 'Wireshark',
                'category' => 'Netzwerk',
                'description' => 'Netzwerk-Protokoll-Analyzer',
                'version' => '4.2'
            ],
            [
                'id' => 108,
                'name' => 'Tableau Desktop',
                'category' => 'Analytics',
                'description' => 'Datenvisualisierung und Business Intelligence',
                'version' => '2023.3'
            ],
            [
                'id' => 109,
                'name' => '1Password',
                'category' => 'Sicherheit',
                'description' => 'Passwort-Manager und sichere Datenspeicherung',
                'version' => '8.10'
            ],
            [
                'id' => 110,
                'name' => 'Jira',
                'category' => 'Projektmanagement',
                'description' => 'Issue-Tracking und Projektmanagement für Teams',
                'version' => '9.12'
            ]
        ];
    }

    /**
     * Get software manufacturers
     */
    private function getSoftwareManufacturers(): array
    {
        return [
            'Microsoft',
            'Adobe',
            'Apple',
            'Google',
            'JetBrains',
            'Atlassian',
            'Figma',
            'Notion',
            'Spotify',
            'Open Source'
        ];
    }

    /**
     * Get software grouped by manufacturer
     */
    private function getSoftwareByManufacturerData(): array
    {
        return [
            'Microsoft' => [
                [
                    'id' => 201,
                    'name' => 'Microsoft Office 365',
                    'category' => 'Büro',
                    'description' => 'Vollständige Office-Suite mit Word, Excel, PowerPoint',
                    'version' => '2024'
                ],
                [
                    'id' => 202,
                    'name' => 'Microsoft Teams',
                    'category' => 'Kommunikation',
                    'description' => 'Team-Zusammenarbeit und Video-Konferenzen',
                    'version' => '1.6'
                ],
                [
                    'id' => 203,
                    'name' => 'Visual Studio Code',
                    'category' => 'Entwicklung',
                    'description' => 'Moderner Code-Editor für verschiedene Programmiersprachen',
                    'version' => '1.85'
                ],
                [
                    'id' => 204,
                    'name' => 'Visual Studio',
                    'category' => 'Entwicklung',
                    'description' => 'Vollständige Entwicklungsumgebung für .NET',
                    'version' => '2022'
                ]
            ],
            'Adobe' => [
                [
                    'id' => 301,
                    'name' => 'Adobe Photoshop',
                    'category' => 'Design',
                    'description' => 'Professionelle Bildbearbeitung',
                    'version' => '2024'
                ],
                [
                    'id' => 302,
                    'name' => 'Adobe Illustrator',
                    'category' => 'Design',
                    'description' => 'Vektorgrafik-Design Software',
                    'version' => '2024'
                ],
                [
                    'id' => 303,
                    'name' => 'Adobe InDesign',
                    'category' => 'Design',
                    'description' => 'Layout und Publishing Software',
                    'version' => '2024'
                ],
                [
                    'id' => 304,
                    'name' => 'Adobe Premiere Pro',
                    'category' => 'Video',
                    'description' => 'Professionelle Videobearbeitung',
                    'version' => '2024'
                ]
            ],
            'Apple' => [
                [
                    'id' => 401,
                    'name' => 'Final Cut Pro',
                    'category' => 'Video',
                    'description' => 'Professionelle Videobearbeitung für macOS',
                    'version' => '10.7'
                ],
                [
                    'id' => 402,
                    'name' => 'Logic Pro',
                    'category' => 'Audio',
                    'description' => 'Professionelle Musik-Produktion',
                    'version' => '10.8'
                ],
                [
                    'id' => 403,
                    'name' => 'Xcode',
                    'category' => 'Entwicklung',
                    'description' => 'Apple Entwicklungsumgebung für iOS und macOS',
                    'version' => '15.2'
                ]
            ],
            'Google' => [
                [
                    'id' => 501,
                    'name' => 'Google Chrome',
                    'category' => 'Browser',
                    'description' => 'Webbrowser von Google',
                    'version' => '121'
                ],
                [
                    'id' => 502,
                    'name' => 'Google Workspace',
                    'category' => 'Büro',
                    'description' => 'Cloud-basierte Office-Suite',
                    'version' => '2024'
                ]
            ],
            'JetBrains' => [
                [
                    'id' => 601,
                    'name' => 'IntelliJ IDEA',
                    'category' => 'Entwicklung',
                    'description' => 'Professionelle Java-Entwicklungsumgebung',
                    'version' => '2024.1'
                ],
                [
                    'id' => 602,
                    'name' => 'PyCharm',
                    'category' => 'Entwicklung',
                    'description' => 'Python-Entwicklungsumgebung',
                    'version' => '2024.1'
                ],
                [
                    'id' => 603,
                    'name' => 'WebStorm',
                    'category' => 'Entwicklung',
                    'description' => 'JavaScript und TypeScript IDE',
                    'version' => '2024.1'
                ]
            ],
            'Atlassian' => [
                [
                    'id' => 701,
                    'name' => 'Jira',
                    'category' => 'Projektmanagement',
                    'description' => 'Issue-Tracking und Projektmanagement',
                    'version' => '9.12'
                ],
                [
                    'id' => 702,
                    'name' => 'Confluence',
                    'category' => 'Dokumentation',
                    'description' => 'Team-Dokumentation und Wiki',
                    'version' => '8.5'
                ]
            ],
            'Figma' => [
                [
                    'id' => 801,
                    'name' => 'Figma',
                    'category' => 'Design',
                    'description' => 'Kollaboratives UI/UX Design Tool',
                    'version' => '116.16'
                ]
            ],
            'Notion' => [
                [
                    'id' => 901,
                    'name' => 'Notion',
                    'category' => 'Produktivität',
                    'description' => 'All-in-One Workspace für Notizen und Projektmanagement',
                    'version' => '3.5'
                ]
            ],
            'Spotify' => [
                [
                    'id' => 1001,
                    'name' => 'Spotify',
                    'category' => 'Multimedia',
                    'description' => 'Musik-Streaming Anwendung',
                    'version' => '1.2.26'
                ]
            ],
            'Open Source' => [
                [
                    'id' => 1101,
                    'name' => 'Blender',
                    'category' => 'Design',
                    'description' => '3D-Modellierung und Animation',
                    'version' => '4.0'
                ],
                [
                    'id' => 1102,
                    'name' => 'OBS Studio',
                    'category' => 'Multimedia',
                    'description' => 'Video-Aufnahme und Live-Streaming',
                    'version' => '30.0'
                ],
                [
                    'id' => 1103,
                    'name' => 'LibreOffice',
                    'category' => 'Büro',
                    'description' => 'Open Source Office-Suite',
                    'version' => '7.6'
                ],
                [
                    'id' => 1104,
                    'name' => 'GIMP',
                    'category' => 'Design',
                    'description' => 'Open Source Bildbearbeitung',
                    'version' => '2.10'
                ]
            ]
        ];
    }
}