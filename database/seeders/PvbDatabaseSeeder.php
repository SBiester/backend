<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PvbDatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database with PVB data.
     */
    public function run(): void
    {
        // First, seed basic reference data
        $this->seedStatus();
        $this->seedVeraenderungArt();
        $this->seedMitarbeiterTyp();
        $this->seedKategorien();
        $this->seedHersteller();
        $this->seedRollengruppen();
        $this->seedSammelrollen();
        
        // Then organizational structure
        $this->seedFunktionen();
        $this->seedTeams();
        $this->seedBereiche();
        $this->seedPositionen();
        
        // Then products and profiles
        $this->seedSoftware();
        $this->seedHardware();
        $this->seedReferenzen();
        
        // Finally employees and orders
        $this->seedMitarbeiter();
        $this->seedVeraenderungen();
        $this->seedAuftraege();
        
        // Link reference profiles to software/hardware
        $this->seedReferenzSoftware();
        $this->seedReferenzHardware();
        $this->seedSammelrollenReferenz();
    }

    private function seedStatus()
    {
        DB::table('tbl_status')->insert([
            ['StatusID' => 1, 'Bezeichnung' => 'ausstehend'],
            ['StatusID' => 2, 'Bezeichnung' => 'in bearbeitung'],
            ['StatusID' => 3, 'Bezeichnung' => 'abgeschlossen'],
            ['StatusID' => 4, 'Bezeichnung' => 'abgebrochen']
        ]);
    }

    private function seedVeraenderungArt()
    {
        DB::table('tbl_veraenderung_art')->insert([
            ['Bezeichnung' => 'Neuer Mitarbeiter'],
            ['Bezeichnung' => 'Mitarbeiteränderung'],
            ['Bezeichnung' => 'Mitarbeiteraustritt'],
            ['Bezeichnung' => 'Verlängerung']
        ]);
    }

    private function seedMitarbeiterTyp()
    {
        DB::table('tbl_ma_typ')->insert([
            ['Bezeichnung' => 'Festangestellt'],
            ['Bezeichnung' => 'Befristet'],
            ['Bezeichnung' => 'Freelancer'],
            ['Bezeichnung' => 'Praktikant'],
            ['Bezeichnung' => 'Werkstudent']
        ]);
    }

    private function seedKategorien()
    {
        DB::table('tbl_kategorie')->insert([
            ['Bezeichnung' => 'Büro'],
            ['Bezeichnung' => 'Design'],
            ['Bezeichnung' => 'Entwicklung'],
            ['Bezeichnung' => 'Kommunikation'],
            ['Bezeichnung' => 'Video'],
            ['Bezeichnung' => 'Audio'],
            ['Bezeichnung' => 'Browser'],
            ['Bezeichnung' => 'Multimedia'],
            ['Bezeichnung' => 'Projektmanagement'],
            ['Bezeichnung' => 'Dokumentation'],
            ['Bezeichnung' => 'Sicherheit']
        ]);
    }

    private function seedHersteller()
    {
        DB::table('tbl_hersteller')->insert([
            ['Bezeichnung' => 'Microsoft'],
            ['Bezeichnung' => 'Adobe'],
            ['Bezeichnung' => 'Apple'],
            ['Bezeichnung' => 'Google'],
            ['Bezeichnung' => 'JetBrains'],
            ['Bezeichnung' => 'Atlassian'],
            ['Bezeichnung' => 'Figma'],
            ['Bezeichnung' => 'Notion'],
            ['Bezeichnung' => 'Open Source'],
            ['Bezeichnung' => 'Dell'],
            ['Bezeichnung' => 'HP'],
            ['Bezeichnung' => 'Lenovo'],
            ['Bezeichnung' => 'Samsung'],
            ['Bezeichnung' => 'LG']
        ]);
    }

    private function seedRollengruppen()
    {
        DB::table('tbl_rollengruppe')->insert([
            ['Bezeichnung' => 'Finanzen'],
            ['Bezeichnung' => 'Personalwesen'],
            ['Bezeichnung' => 'Einkauf'],
            ['Bezeichnung' => 'Vertrieb'],
            ['Bezeichnung' => 'Logistik'],
            ['Bezeichnung' => 'Controlling'],
            ['Bezeichnung' => 'IT-Administration']
        ]);
    }

    private function seedSammelrollen()
    {
        $rollen = [
            ['Bezeichnung' => 'SAP_FI_BASIS', 'Schluessel' => 'FI001', 'RollengruppeID' => 1],
            ['Bezeichnung' => 'SAP_FI_BUCHUNG', 'Schluessel' => 'FI002', 'RollengruppeID' => 1],
            ['Bezeichnung' => 'SAP_HR_BASIS', 'Schluessel' => 'HR001', 'RollengruppeID' => 2],
            ['Bezeichnung' => 'SAP_HR_ADMIN', 'Schluessel' => 'HR002', 'RollengruppeID' => 2],
            ['Bezeichnung' => 'SAP_MM_BASIS', 'Schluessel' => 'MM001', 'RollengruppeID' => 3],
            ['Bezeichnung' => 'SAP_SD_BASIS', 'Schluessel' => 'SD001', 'RollengruppeID' => 4],
            ['Bezeichnung' => 'SAP_WM_BASIS', 'Schluessel' => 'WM001', 'RollengruppeID' => 5],
            ['Bezeichnung' => 'SAP_CO_BASIS', 'Schluessel' => 'CO001', 'RollengruppeID' => 6]
        ];

        DB::table('tbl_sammelrollen')->insert($rollen);
    }

    private function seedFunktionen()
    {
        DB::table('tbl_funktion')->insert([
            ['Bezeichnung' => 'Entwicklung'],
            ['Bezeichnung' => 'Design'],
            ['Bezeichnung' => 'Marketing'],
            ['Bezeichnung' => 'Vertrieb'],
            ['Bezeichnung' => 'Personal'],
            ['Bezeichnung' => 'Finanzen'],
            ['Bezeichnung' => 'IT-Administration']
        ]);
    }

    private function seedTeams()
    {
        DB::table('tbl_team')->insert([
            ['Bezeichnung' => 'Frontend Team', 'FunktionID' => 1],
            ['Bezeichnung' => 'Backend Team', 'FunktionID' => 1],
            ['Bezeichnung' => 'UI/UX Team', 'FunktionID' => 2],
            ['Bezeichnung' => 'Content Team', 'FunktionID' => 3],
            ['Bezeichnung' => 'Sales Team', 'FunktionID' => 4],
            ['Bezeichnung' => 'HR Team', 'FunktionID' => 5],
            ['Bezeichnung' => 'Finance Team', 'FunktionID' => 6],
            ['Bezeichnung' => 'System Admin', 'FunktionID' => 7]
        ]);
    }

    private function seedBereiche()
    {
        DB::table('tbl_bereich')->insert([
            ['Bezeichnung' => 'Web Development', 'TeamID' => 1],
            ['Bezeichnung' => 'Mobile Development', 'TeamID' => 1],
            ['Bezeichnung' => 'API Development', 'TeamID' => 2],
            ['Bezeichnung' => 'DevOps', 'TeamID' => 2],
            ['Bezeichnung' => 'User Experience', 'TeamID' => 3],
            ['Bezeichnung' => 'Visual Design', 'TeamID' => 3],
            ['Bezeichnung' => 'Content Marketing', 'TeamID' => 4],
            ['Bezeichnung' => 'Kundenbetreuung', 'TeamID' => 5],
            ['Bezeichnung' => 'Recruiting', 'TeamID' => 6],
            ['Bezeichnung' => 'Buchhaltung', 'TeamID' => 7],
            ['Bezeichnung' => 'IT-Support', 'TeamID' => 8]
        ]);
    }

    private function seedPositionen()
    {
        DB::table('tbl_position')->insert([
            ['Bezeichnung' => 'Junior Entwickler'],
            ['Bezeichnung' => 'Senior Entwickler'],
            ['Bezeichnung' => 'Lead Entwickler'],
            ['Bezeichnung' => 'Designer'],
            ['Bezeichnung' => 'Senior Designer'],
            ['Bezeichnung' => 'Marketing Manager'],
            ['Bezeichnung' => 'Sales Manager'],
            ['Bezeichnung' => 'HR Manager'],
            ['Bezeichnung' => 'Buchhalter'],
            ['Bezeichnung' => 'IT-Administrator'],
            ['Bezeichnung' => 'Projektmanager']
        ]);
    }

    private function seedSoftware()
    {
        $software = [
            // Microsoft
            ['Bezeichnung' => 'Microsoft Office 365', 'HerstellerID' => 1, 'KategorieID' => 1, 'Sammelrollen' => false, 'aktiv' => true],
            ['Bezeichnung' => 'Microsoft Teams', 'HerstellerID' => 1, 'KategorieID' => 4, 'Sammelrollen' => false, 'aktiv' => true],
            ['Bezeichnung' => 'Visual Studio Code', 'HerstellerID' => 1, 'KategorieID' => 3, 'Sammelrollen' => false, 'aktiv' => true],
            ['Bezeichnung' => 'Visual Studio', 'HerstellerID' => 1, 'KategorieID' => 3, 'Sammelrollen' => false, 'aktiv' => true],
            
            // Adobe
            ['Bezeichnung' => 'Adobe Photoshop', 'HerstellerID' => 2, 'KategorieID' => 2, 'Sammelrollen' => false, 'aktiv' => true],
            ['Bezeichnung' => 'Adobe Illustrator', 'HerstellerID' => 2, 'KategorieID' => 2, 'Sammelrollen' => false, 'aktiv' => true],
            ['Bezeichnung' => 'Adobe InDesign', 'HerstellerID' => 2, 'KategorieID' => 2, 'Sammelrollen' => false, 'aktiv' => true],
            ['Bezeichnung' => 'Adobe Premiere Pro', 'HerstellerID' => 2, 'KategorieID' => 5, 'Sammelrollen' => false, 'aktiv' => true],
            
            // Apple
            ['Bezeichnung' => 'Final Cut Pro', 'HerstellerID' => 3, 'KategorieID' => 5, 'Sammelrollen' => false, 'aktiv' => true],
            ['Bezeichnung' => 'Logic Pro', 'HerstellerID' => 3, 'KategorieID' => 6, 'Sammelrollen' => false, 'aktiv' => true],
            ['Bezeichnung' => 'Xcode', 'HerstellerID' => 3, 'KategorieID' => 3, 'Sammelrollen' => false, 'aktiv' => true],
            
            // JetBrains
            ['Bezeichnung' => 'IntelliJ IDEA', 'HerstellerID' => 5, 'KategorieID' => 3, 'Sammelrollen' => false, 'aktiv' => true],
            ['Bezeichnung' => 'PyCharm', 'HerstellerID' => 5, 'KategorieID' => 3, 'Sammelrollen' => false, 'aktiv' => true],
            ['Bezeichnung' => 'WebStorm', 'HerstellerID' => 5, 'KategorieID' => 3, 'Sammelrollen' => false, 'aktiv' => true],
            
            // Others
            ['Bezeichnung' => 'Figma', 'HerstellerID' => 7, 'KategorieID' => 2, 'Sammelrollen' => false, 'aktiv' => true],
            ['Bezeichnung' => 'Notion', 'HerstellerID' => 8, 'KategorieID' => 9, 'Sammelrollen' => false, 'aktiv' => true],
            ['Bezeichnung' => 'Google Chrome', 'HerstellerID' => 4, 'KategorieID' => 7, 'Sammelrollen' => false, 'aktiv' => true],
            ['Bezeichnung' => 'Slack', 'HerstellerID' => 9, 'KategorieID' => 4, 'Sammelrollen' => false, 'aktiv' => true]
        ];

        DB::table('tbl_software')->insert($software);
    }

    private function seedHardware()
    {
        $hardware = [
            ['Bezeichnung' => 'MacBook Pro 16"', 'Hersteller' => 'Apple'],
            ['Bezeichnung' => 'MacBook Air 13"', 'Hersteller' => 'Apple'],
            ['Bezeichnung' => 'iMac 27"', 'Hersteller' => 'Apple'],
            ['Bezeichnung' => 'Dell XPS 13', 'Hersteller' => 'Dell'],
            ['Bezeichnung' => 'Dell OptiPlex 7090', 'Hersteller' => 'Dell'],
            ['Bezeichnung' => 'HP EliteBook 840', 'Hersteller' => 'HP'],
            ['Bezeichnung' => 'HP Z4 G5 Workstation', 'Hersteller' => 'HP'],
            ['Bezeichnung' => 'ThinkPad X1 Carbon', 'Hersteller' => 'Lenovo'],
            ['Bezeichnung' => 'Samsung Monitor 32"', 'Hersteller' => 'Samsung'],
            ['Bezeichnung' => 'LG UltraWide 34"', 'Hersteller' => 'LG']
        ];

        DB::table('tbl_hardware')->insert($hardware);
    }

    private function seedReferenzen()
    {
        DB::table('tbl_referenz')->insert([
            ['Bezeichnung' => 'Böhm Finn', 'BereichID' => 1, 'aktiv' => true],
            ['Bezeichnung' => 'Wirschinin Elena', 'BereichID' => 5, 'aktiv' => true],
            ['Bezeichnung' => 'Breidenbicher Kjell', 'BereichID' => 3, 'aktiv' => true],
            ['Bezeichnung' => 'Saed Abdo', 'BereichID' => 2, 'aktiv' => true],
            ['Bezeichnung' => 'Standard Büro', 'BereichID' => 10, 'aktiv' => true]
        ]);
    }

    private function seedMitarbeiter()
    {
        DB::table('tbl_ma')->insert([
            [
                'MA_Nummer' => 'MA001',
                'Vorname' => 'Max',
                'Name' => 'Mustermann',
                'Funktion' => 'Entwickler',
                'Vorgesetzter' => 'John Doe',
                'MA_TypID' => 1,
                'BereichID' => 1,
                'PositionID' => 2
            ],
            [
                'MA_Nummer' => 'MA002',
                'Vorname' => 'Anna',
                'Name' => 'Schmidt',
                'Funktion' => 'Designer',
                'Vorgesetzter' => 'Jane Smith',
                'MA_TypID' => 1,
                'BereichID' => 5,
                'PositionID' => 5
            ],
            [
                'MA_Nummer' => 'MA003',
                'Vorname' => 'Peter',
                'Name' => 'Wagner',
                'Funktion' => 'Marketing',
                'Vorgesetzter' => 'Bob Johnson',
                'MA_TypID' => 1,
                'BereichID' => 7,
                'PositionID' => 6
            ]
        ]);
    }

    private function seedVeraenderungen()
    {
        DB::table('tbl_veraenderung')->insert([
            [
                'Veraenderung_ArtID' => 2,
                'AenderungZum' => '2024-01-15',
                'BefristetBis' => null,
                'Unternehmen' => 'Beispiel GmbH'
            ],
            [
                'Veraenderung_ArtID' => 1,
                'AenderungZum' => '2024-01-20',
                'BefristetBis' => '2024-12-31',
                'Unternehmen' => 'Beispiel GmbH'
            ]
        ]);
    }

    private function seedAuftraege()
    {
        DB::table('tbl_auftrag')->insert([
            [
                'VeraenderungID' => 1,
                'MAID' => 1,
                'AuftragDatum' => '2024-01-15',
                'AuftragMA' => 'admin@company.com',
                'StatusID' => 1,
                'Kommentar' => 'Beförderung zu Senior Entwickler'
            ],
            [
                'VeraenderungID' => 1,
                'MAID' => 2,
                'AuftragDatum' => '2024-01-14',
                'AuftragMA' => 'admin@company.com',
                'StatusID' => 2,
                'Kommentar' => 'Hardware-Upgrade erforderlich'
            ],
            [
                'VeraenderungID' => 2,
                'MAID' => 3,
                'AuftragDatum' => '2024-01-10',
                'AuftragMA' => 'admin@company.com',
                'StatusID' => 1,
                'Kommentar' => 'Neuer Mitarbeiter im Marketing'
            ]
        ]);
    }

    private function seedReferenzSoftware()
    {
        // Böhm Finn (Web Development) - Software IDs 1,3,15,17,18
        DB::table('tbl_referenz_software')->insert([
            ['ReferenzID' => 1, 'SoftwareID' => 1], // Office 365
            ['ReferenzID' => 1, 'SoftwareID' => 3], // VS Code
            ['ReferenzID' => 1, 'SoftwareID' => 15], // Figma
            ['ReferenzID' => 1, 'SoftwareID' => 17], // Chrome
            ['ReferenzID' => 1, 'SoftwareID' => 18], // Slack
            
            // Wirschinin Elena (UX Design) - Software IDs 5,6,15,16
            ['ReferenzID' => 2, 'SoftwareID' => 5], // Photoshop
            ['ReferenzID' => 2, 'SoftwareID' => 6], // Illustrator
            ['ReferenzID' => 2, 'SoftwareID' => 15], // Figma
            ['ReferenzID' => 2, 'SoftwareID' => 16], // Notion
            
            // Breidenbicher Kjell (API Development) - Software IDs 12,13,14,17
            ['ReferenzID' => 3, 'SoftwareID' => 12], // IntelliJ
            ['ReferenzID' => 3, 'SoftwareID' => 13], // PyCharm
            ['ReferenzID' => 3, 'SoftwareID' => 14], // WebStorm
            ['ReferenzID' => 3, 'SoftwareID' => 17], // Chrome
            
            // Saed Abdo (Mobile Development) - Software IDs 1,11,17
            ['ReferenzID' => 4, 'SoftwareID' => 1], // Office 365
            ['ReferenzID' => 4, 'SoftwareID' => 11], // Xcode
            ['ReferenzID' => 4, 'SoftwareID' => 17], // Chrome
            
            // Standard Büro - Software IDs 1,2,17
            ['ReferenzID' => 5, 'SoftwareID' => 1], // Office 365
            ['ReferenzID' => 5, 'SoftwareID' => 2], // Teams
            ['ReferenzID' => 5, 'SoftwareID' => 17], // Chrome
        ]);
    }

    private function seedReferenzHardware()
    {
        // Assign hardware to reference profiles
        DB::table('tbl_referenz_hardware')->insert([
            ['ReferenzID' => 1, 'HardwareID' => 1], // Böhm Finn - MacBook Pro
            ['ReferenzID' => 1, 'HardwareID' => 9], // + Samsung Monitor
            
            ['ReferenzID' => 2, 'HardwareID' => 3], // Wirschinin Elena - iMac
            ['ReferenzID' => 2, 'HardwareID' => 10], // + LG UltraWide
            
            ['ReferenzID' => 3, 'HardwareID' => 7], // Breidenbicher Kjell - HP Workstation
            ['ReferenzID' => 3, 'HardwareID' => 9], // + Samsung Monitor
            
            ['ReferenzID' => 4, 'HardwareID' => 2], // Saed Abdo - MacBook Air
            ['ReferenzID' => 4, 'HardwareID' => 9], // + Samsung Monitor
            
            ['ReferenzID' => 5, 'HardwareID' => 5], // Standard Büro - Dell OptiPlex
            ['ReferenzID' => 5, 'HardwareID' => 9], // + Samsung Monitor
        ]);
    }

    private function seedSammelrollenReferenz()
    {
        // Assign SAP roles to reference profiles
        DB::table('tbl_sammelrollen_referenz')->insert([
            ['ReferenzID' => 5, 'SammelrollenID' => 1], // Standard Büro - FI Basis
            ['ReferenzID' => 5, 'SammelrollenID' => 3], // Standard Büro - HR Basis
        ]);
    }
}