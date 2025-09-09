<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class HardwareController extends Controller
{
    /**
     * Get hardware items based on reference profile
     */
    public function getHardwareByProfile(Request $request): JsonResponse
    {
        $profileName = $request->input('profile');
        
        if (!$profileName) {
            return response()->json(['error' => 'Profile parameter is required'], 400);
        }

        // Beispieldaten für Hardware basierend auf Referenzprofilen
        $hardwareData = $this->getHardwareMapping();
        
        if (!isset($hardwareData[$profileName])) {
            return response()->json(['error' => 'Profile not found'], 404);
        }

        return response()->json([
            'profile' => $profileName,
            'hardware' => $hardwareData[$profileName]
        ]);
    }

    /**
     * Get all available hardware items
     */
    public function getAllHardware(): JsonResponse
    {
        return response()->json([
            'hardware' => $this->getAllHardwareItems()
        ]);
    }

    /**
     * Get additional hardware items for selection
     */
    public function getAdditionalHardware(): JsonResponse
    {
        return response()->json([
            'hardware' => $this->getAdditionalHardwareItems()
        ]);
    }

    /**
     * Get hardware categories
     */
    public function getCategories(): JsonResponse
    {
        return response()->json([
            'categories' => $this->getHardwareCategories()
        ]);
    }

    /**
     * Get hardware by category
     */
    public function getHardwareByCategory(Request $request): JsonResponse
    {
        $category = $request->input('category');
        
        if (!$category) {
            return response()->json(['error' => 'Category parameter is required'], 400);
        }

        $hardwareByCategory = $this->getHardwareByCategoryData();
        
        if (!isset($hardwareByCategory[$category])) {
            return response()->json(['error' => 'Category not found'], 404);
        }

        return response()->json([
            'category' => $category,
            'hardware' => $hardwareByCategory[$category]
        ]);
    }

    /**
     * Hardware mapping for reference profiles
     */
    private function getHardwareMapping(): array
    {
        return [
            'Böhm Finn' => [
                [
                    'id' => 1,
                    'name' => 'Dell Laptop Precision 5570',
                    'category' => 'Laptop',
                    'specifications' => 'Intel i7, 16GB RAM, 512GB SSD',
                    'assigned' => true
                ],
                [
                    'id' => 2,
                    'name' => 'Dell Monitor U2722D',
                    'category' => 'Monitor',
                    'specifications' => '27" 4K USB-C',
                    'assigned' => true
                ],
                [
                    'id' => 3,
                    'name' => 'Logitech MX Keys',
                    'category' => 'Tastatur',
                    'specifications' => 'Wireless, beleuchtete Tasten',
                    'assigned' => true
                ],
                [
                    'id' => 4,
                    'name' => 'Logitech MX Master 3S',
                    'category' => 'Maus',
                    'specifications' => 'Wireless, präzisionssensor',
                    'assigned' => true
                ],
                [
                    'id' => 5,
                    'name' => 'iPhone 14 Pro',
                    'category' => 'Smartphone',
                    'specifications' => '128GB, iOS 17',
                    'assigned' => true
                ]
            ],
            'Wirschinin Elena' => [
                [
                    'id' => 6,
                    'name' => 'MacBook Pro 16"',
                    'category' => 'Laptop',
                    'specifications' => 'M2 Pro, 32GB RAM, 1TB SSD',
                    'assigned' => true
                ],
                [
                    'id' => 7,
                    'name' => 'Apple Studio Display',
                    'category' => 'Monitor',
                    'specifications' => '27" 5K Retina Display',
                    'assigned' => true
                ],
                [
                    'id' => 8,
                    'name' => 'Apple Magic Keyboard',
                    'category' => 'Tastatur',
                    'specifications' => 'Wireless, deutsches Layout',
                    'assigned' => true
                ],
                [
                    'id' => 9,
                    'name' => 'Apple Magic Mouse',
                    'category' => 'Maus',
                    'specifications' => 'Multi-Touch Oberfläche',
                    'assigned' => true
                ],
                [
                    'id' => 10,
                    'name' => 'iPad Pro 12.9"',
                    'category' => 'Tablet',
                    'specifications' => 'M2 Chip, 512GB, WiFi + Cellular',
                    'assigned' => true
                ]
            ],
            'Breidenbicher Kjell' => [
                [
                    'id' => 11,
                    'name' => 'Lenovo ThinkPad T14s',
                    'category' => 'Laptop',
                    'specifications' => 'AMD Ryzen 7, 16GB RAM, 512GB SSD',
                    'assigned' => true
                ],
                [
                    'id' => 12,
                    'name' => 'Samsung Monitor M7',
                    'category' => 'Monitor',
                    'specifications' => '32" 4K Smart Monitor',
                    'assigned' => true
                ],
                [
                    'id' => 13,
                    'name' => 'Microsoft Surface Keyboard',
                    'category' => 'Tastatur',
                    'specifications' => 'Bluetooth, flaches Design',
                    'assigned' => true
                ],
                [
                    'id' => 14,
                    'name' => 'Microsoft Surface Mouse',
                    'category' => 'Maus',
                    'specifications' => 'Bluetooth, ergonomisches Design',
                    'assigned' => true
                ],
                [
                    'id' => 15,
                    'name' => 'Samsung Galaxy S23',
                    'category' => 'Smartphone',
                    'specifications' => '256GB, Android 14',
                    'assigned' => true
                ]
            ],
            'Saed Abdo' => [
                [
                    'id' => 16,
                    'name' => 'HP EliteBook 850 G9',
                    'category' => 'Laptop',
                    'specifications' => 'Intel i5, 8GB RAM, 256GB SSD',
                    'assigned' => true
                ],
                [
                    'id' => 17,
                    'name' => 'HP E24 G5 Monitor',
                    'category' => 'Monitor',
                    'specifications' => '24" Full HD, höhenverstellbar',
                    'assigned' => true
                ],
                [
                    'id' => 18,
                    'name' => 'Standard USB Tastatur',
                    'category' => 'Tastatur',
                    'specifications' => 'Verkabelt, deutsches Layout',
                    'assigned' => true
                ],
                [
                    'id' => 19,
                    'name' => 'Standard USB Maus',
                    'category' => 'Maus',
                    'specifications' => 'Verkabelt, optischer Sensor',
                    'assigned' => true
                ],
                [
                    'id' => 20,
                    'name' => 'Basis Smartphone',
                    'category' => 'Smartphone',
                    'specifications' => '64GB, Android 13',
                    'assigned' => true
                ]
            ]
        ];
    }

    /**
     * Get all available hardware items
     */
    private function getAllHardwareItems(): array
    {
        $allHardware = [];
        foreach ($this->getHardwareMapping() as $profile => $hardware) {
            $allHardware = array_merge($allHardware, $hardware);
        }
        return $allHardware;
    }

    /**
     * Get additional hardware items for selection
     */
    private function getAdditionalHardwareItems(): array
    {
        return [
            [
                'id' => 101,
                'name' => 'Dell Webcam WB7022',
                'category' => 'Webcam',
                'specifications' => '4K Ultra HD, Auto-Fokus',
                'assigned' => false
            ],
            [
                'id' => 102,
                'name' => 'Jabra Evolve2 75',
                'category' => 'Headset',
                'specifications' => 'Wireless, Noise Cancelling, USB-A/C',
                'assigned' => false
            ],
            [
                'id' => 103,
                'name' => 'Samsung T7 Portable SSD',
                'category' => 'Speicher',
                'specifications' => '1TB, USB 3.2, verschlüsselt',
                'assigned' => false
            ],
            [
                'id' => 104,
                'name' => 'Anker PowerCore 26800',
                'category' => 'Powerbank',
                'specifications' => '26800mAh, 3 USB Ports',
                'assigned' => false
            ],
            [
                'id' => 105,
                'name' => 'Elgato Stream Deck',
                'category' => 'Controller',
                'specifications' => '15 LCD-Tasten, programmierbar',
                'assigned' => false
            ],
            [
                'id' => 106,
                'name' => 'Wacom Intuos Pro M',
                'category' => 'Grafiktablet',
                'specifications' => 'Medium, 8192 Druckstufen, Bluetooth',
                'assigned' => false
            ],
            [
                'id' => 107,
                'name' => 'Synology DS220+',
                'category' => 'NAS',
                'specifications' => '2-Bay, Intel Celeron, 2GB RAM',
                'assigned' => false
            ],
            [
                'id' => 108,
                'name' => 'Brother HL-L2350DW',
                'category' => 'Drucker',
                'specifications' => 'Laserdrucker, WiFi, Duplex',
                'assigned' => false
            ],
            [
                'id' => 109,
                'name' => 'APC Back-UPS Pro 1500',
                'category' => 'USV',
                'specifications' => '1500VA/865W, LCD Display',
                'assigned' => false
            ],
            [
                'id' => 110,
                'name' => 'Logitech C920s HD Pro',
                'category' => 'Webcam',
                'specifications' => '1080p HD, Stereo Audio, Privacy Shutter',
                'assigned' => false
            ]
        ];
    }

    /**
     * Get hardware categories
     */
    private function getHardwareCategories(): array
    {
        return [
            'Laptops',
            'Smartphones',
            'Peripherie'
        ];
    }

    /**
     * Get hardware grouped by category
     */
    private function getHardwareByCategoryData(): array
    {
        return [
            'Laptops' => [
                [
                    'id' => 2001,
                    'name' => 'Dell Laptop Precision 5570',
                    'category' => 'Laptop',
                    'specifications' => 'Intel i7-12700H, 32GB RAM, 1TB SSD',
                    'assigned' => false
                ],
                [
                    'id' => 2002,
                    'name' => 'MacBook Pro 16"',
                    'category' => 'Laptop',
                    'specifications' => 'M2 Pro, 32GB RAM, 1TB SSD',
                    'assigned' => false
                ],
                [
                    'id' => 2003,
                    'name' => 'Lenovo ThinkPad T14s',
                    'category' => 'Laptop',
                    'specifications' => 'AMD Ryzen 7, 16GB RAM, 512GB SSD',
                    'assigned' => false
                ],
                [
                    'id' => 2004,
                    'name' => 'HP EliteBook 850 G9',
                    'category' => 'Laptop',
                    'specifications' => 'Intel i7-1260P, 16GB RAM, 512GB SSD',
                    'assigned' => false
                ],
                [
                    'id' => 2005,
                    'name' => 'Surface Laptop 5',
                    'category' => 'Laptop',
                    'specifications' => 'Intel i7-1255U, 16GB RAM, 512GB SSD',
                    'assigned' => false
                ]
            ],
            'Smartphones' => [
                [
                    'id' => 3001,
                    'name' => 'iPhone 15 Pro',
                    'category' => 'Smartphone',
                    'specifications' => '256GB, iOS 17, A17 Pro Chip',
                    'assigned' => false
                ],
                [
                    'id' => 3002,
                    'name' => 'Samsung Galaxy S24',
                    'category' => 'Smartphone',
                    'specifications' => '256GB, Android 14, Snapdragon 8 Gen 3',
                    'assigned' => false
                ],
                [
                    'id' => 3003,
                    'name' => 'Google Pixel 8 Pro',
                    'category' => 'Smartphone',
                    'specifications' => '128GB, Android 14, Tensor G3',
                    'assigned' => false
                ],
                [
                    'id' => 3004,
                    'name' => 'iPhone 15',
                    'category' => 'Smartphone',
                    'specifications' => '128GB, iOS 17, A16 Bionic',
                    'assigned' => false
                ],
                [
                    'id' => 3005,
                    'name' => 'Fairphone 5',
                    'category' => 'Smartphone',
                    'specifications' => '256GB, Android 13, nachhaltig produziert',
                    'assigned' => false
                ]
            ],
            'Peripherie' => [
                [
                    'id' => 4001,
                    'name' => 'Dell Monitor U2722D',
                    'category' => 'Monitor',
                    'specifications' => '27" 4K USB-C, höhenverstellbar',
                    'assigned' => false
                ],
                [
                    'id' => 4002,
                    'name' => 'Logitech MX Keys',
                    'category' => 'Tastatur',
                    'specifications' => 'Wireless, beleuchtete Tasten, USB-C',
                    'assigned' => false
                ],
                [
                    'id' => 4003,
                    'name' => 'Logitech MX Master 3S',
                    'category' => 'Maus',
                    'specifications' => 'Wireless, präzisionssensor, USB-C',
                    'assigned' => false
                ],
                [
                    'id' => 4004,
                    'name' => 'Dell Webcam WB7022',
                    'category' => 'Webcam',
                    'specifications' => '4K Ultra HD, Auto-Fokus, USB-A',
                    'assigned' => false
                ],
                [
                    'id' => 4005,
                    'name' => 'Jabra Evolve2 75',
                    'category' => 'Headset',
                    'specifications' => 'Wireless, Noise Cancelling, USB-A/C',
                    'assigned' => false
                ],
                [
                    'id' => 4006,
                    'name' => 'Samsung T7 Portable SSD',
                    'category' => 'Speicher',
                    'specifications' => '1TB, USB 3.2, verschlüsselt',
                    'assigned' => false
                ],
                [
                    'id' => 4007,
                    'name' => 'Apple Magic Keyboard',
                    'category' => 'Tastatur',
                    'specifications' => 'Wireless, deutsches Layout, Lightning',
                    'assigned' => false
                ],
                [
                    'id' => 4008,
                    'name' => 'Wacom Intuos Pro M',
                    'category' => 'Grafiktablet',
                    'specifications' => 'Medium, 8192 Druckstufen, Bluetooth',
                    'assigned' => false
                ]
            ]
        ];
    }
}