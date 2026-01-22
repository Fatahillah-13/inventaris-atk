<?php

namespace Database\Seeders;

use App\Models\Division;
use App\Models\Item;
use App\Models\ItemCategory;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AtkPhase1TestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create divisions
        $itDivision = Division::firstOrCreate(
            ['kode' => 'IT'],
            ['nama' => 'IT Department']
        );

        $hrDivision = Division::firstOrCreate(
            ['kode' => 'HR'],
            ['nama' => 'Human Resources']
        );

        $financeDivision = Division::firstOrCreate(
            ['kode' => 'FIN'],
            ['nama' => 'Finance']
        );

        // Create item categories
        $writingCategory = ItemCategory::firstOrCreate(
            ['kode' => 'ATK-TULIS'],
            ['nama' => 'Alat Tulis', 'is_active' => true]
        );

        $paperCategory = ItemCategory::firstOrCreate(
            ['kode' => 'ATK-KERTAS'],
            ['nama' => 'Kertas', 'is_active' => true]
        );

        $officeCategory = ItemCategory::firstOrCreate(
            ['kode' => 'ATK-KANTOR'],
            ['nama' => 'Perlengkapan Kantor', 'is_active' => true]
        );

        // Create requestable items
        $items = [
            [
                'kode_barang' => 'ATK-001',
                'nama_barang' => 'Pulpen Hitam Standard',
                'category_id' => $writingCategory->id,
                'satuan' => 'pcs',
                'stok_awal' => 500,
                'stok_terkini' => 450,
                'is_requestable' => true,
            ],
            [
                'kode_barang' => 'ATK-002',
                'nama_barang' => 'Pulpen Biru Standard',
                'category_id' => $writingCategory->id,
                'satuan' => 'pcs',
                'stok_awal' => 500,
                'stok_terkini' => 470,
                'is_requestable' => true,
            ],
            [
                'kode_barang' => 'ATK-003',
                'nama_barang' => 'Pensil 2B',
                'category_id' => $writingCategory->id,
                'satuan' => 'pcs',
                'stok_awal' => 300,
                'stok_terkini' => 280,
                'is_requestable' => true,
            ],
            [
                'kode_barang' => 'ATK-004',
                'nama_barang' => 'Spidol Boardmarker Hitam',
                'category_id' => $writingCategory->id,
                'satuan' => 'pcs',
                'stok_awal' => 100,
                'stok_terkini' => 85,
                'is_requestable' => true,
            ],
            [
                'kode_barang' => 'ATK-005',
                'nama_barang' => 'Spidol Boardmarker Merah',
                'category_id' => $writingCategory->id,
                'satuan' => 'pcs',
                'stok_awal' => 100,
                'stok_terkini' => 90,
                'is_requestable' => true,
            ],
            [
                'kode_barang' => 'ATK-006',
                'nama_barang' => 'Kertas A4 80gram',
                'category_id' => $paperCategory->id,
                'satuan' => 'rim',
                'stok_awal' => 200,
                'stok_terkini' => 150,
                'is_requestable' => true,
            ],
            [
                'kode_barang' => 'ATK-007',
                'nama_barang' => 'Kertas F4 80gram',
                'category_id' => $paperCategory->id,
                'satuan' => 'rim',
                'stok_awal' => 100,
                'stok_terkini' => 80,
                'is_requestable' => true,
            ],
            [
                'kode_barang' => 'ATK-008',
                'nama_barang' => 'Stapler Sedang',
                'category_id' => $officeCategory->id,
                'satuan' => 'pcs',
                'stok_awal' => 50,
                'stok_terkini' => 35,
                'is_requestable' => true,
            ],
            [
                'kode_barang' => 'ATK-009',
                'nama_barang' => 'Isi Stapler No.10',
                'category_id' => $officeCategory->id,
                'satuan' => 'box',
                'stok_awal' => 100,
                'stok_terkini' => 75,
                'is_requestable' => true,
            ],
            [
                'kode_barang' => 'ATK-010',
                'nama_barang' => 'Penghapus Pensil',
                'category_id' => $writingCategory->id,
                'satuan' => 'pcs',
                'stok_awal' => 200,
                'stok_terkini' => 180,
                'is_requestable' => true,
            ],
            [
                'kode_barang' => 'ATK-011',
                'nama_barang' => 'Correction Tape',
                'category_id' => $writingCategory->id,
                'satuan' => 'pcs',
                'stok_awal' => 100,
                'stok_terkini' => 85,
                'is_requestable' => true,
            ],
            [
                'kode_barang' => 'ATK-012',
                'nama_barang' => 'Post-it Notes (Sticky Notes)',
                'category_id' => $officeCategory->id,
                'satuan' => 'pad',
                'stok_awal' => 150,
                'stok_terkini' => 120,
                'is_requestable' => true,
            ],
            [
                'kode_barang' => 'ATK-013',
                'nama_barang' => 'Map Plastik',
                'category_id' => $officeCategory->id,
                'satuan' => 'pcs',
                'stok_awal' => 200,
                'stok_terkini' => 150,
                'is_requestable' => true,
            ],
            [
                'kode_barang' => 'ATK-014',
                'nama_barang' => 'Gunting Kertas',
                'category_id' => $officeCategory->id,
                'satuan' => 'pcs',
                'stok_awal' => 50,
                'stok_terkini' => 40,
                'is_requestable' => true,
            ],
            [
                'kode_barang' => 'ATK-015',
                'nama_barang' => 'Cutter Kecil',
                'category_id' => $officeCategory->id,
                'satuan' => 'pcs',
                'stok_awal' => 50,
                'stok_terkini' => 35,
                'is_requestable' => true,
            ],
            [
                'kode_barang' => 'ATK-016',
                'nama_barang' => 'Penggaris 30cm',
                'category_id' => $officeCategory->id,
                'satuan' => 'pcs',
                'stok_awal' => 80,
                'stok_terkini' => 65,
                'is_requestable' => true,
            ],
            [
                'kode_barang' => 'ATK-017',
                'nama_barang' => 'Binder Clip Sedang',
                'category_id' => $officeCategory->id,
                'satuan' => 'box',
                'stok_awal' => 100,
                'stok_terkini' => 80,
                'is_requestable' => true,
            ],
            [
                'kode_barang' => 'ATK-018',
                'nama_barang' => 'Paper Clip',
                'category_id' => $officeCategory->id,
                'satuan' => 'box',
                'stok_awal' => 100,
                'stok_terkini' => 75,
                'is_requestable' => true,
            ],
            [
                'kode_barang' => 'ATK-019',
                'nama_barang' => 'Amplop Coklat',
                'category_id' => $officeCategory->id,
                'satuan' => 'pack',
                'stok_awal' => 50,
                'stok_terkini' => 40,
                'is_requestable' => true,
            ],
            [
                'kode_barang' => 'ATK-020',
                'nama_barang' => 'Lakban Bening',
                'category_id' => $officeCategory->id,
                'satuan' => 'roll',
                'stok_awal' => 50,
                'stok_terkini' => 35,
                'is_requestable' => true,
            ],
            // Non-requestable items
            [
                'kode_barang' => 'EQP-001',
                'nama_barang' => 'Printer Laser',
                'category_id' => $officeCategory->id,
                'satuan' => 'unit',
                'stok_awal' => 10,
                'stok_terkini' => 8,
                'is_requestable' => false,
                'can_be_loaned' => true,
            ],
            [
                'kode_barang' => 'EQP-002',
                'nama_barang' => 'Scanner',
                'category_id' => $officeCategory->id,
                'satuan' => 'unit',
                'stok_awal' => 5,
                'stok_terkini' => 4,
                'is_requestable' => false,
                'can_be_loaned' => true,
            ],
        ];

        foreach ($items as $itemData) {
            Item::firstOrCreate(
                ['kode_barang' => $itemData['kode_barang']],
                $itemData
            );
        }

        // Create test users
        User::firstOrCreate(
            ['email' => 'admin@inventaris.test'],
            [
                'name' => 'Admin User',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'division_id' => null,
            ]
        );

        User::firstOrCreate(
            ['email' => 'staff.it@inventaris.test'],
            [
                'name' => 'IT Staff',
                'password' => Hash::make('password'),
                'role' => 'staff_pengelola',
                'division_id' => $itDivision->id,
            ]
        );

        User::firstOrCreate(
            ['email' => 'staff.hr@inventaris.test'],
            [
                'name' => 'HR Staff',
                'password' => Hash::make('password'),
                'role' => 'staff_pengelola',
                'division_id' => $hrDivision->id,
            ]
        );

        User::firstOrCreate(
            ['email' => 'staff.finance@inventaris.test'],
            [
                'name' => 'Finance Staff',
                'password' => Hash::make('password'),
                'role' => 'staff_pengelola',
                'division_id' => $financeDivision->id,
            ]
        );

        $this->command->info('Test data seeded successfully!');
        $this->command->info('Test users created:');
        $this->command->info('  - admin@inventaris.test (password: password) - Admin');
        $this->command->info('  - staff.it@inventaris.test (password: password) - IT Staff');
        $this->command->info('  - staff.hr@inventaris.test (password: password) - HR Staff');
        $this->command->info('  - staff.finance@inventaris.test (password: password) - Finance Staff');
        $this->command->info('Total requestable items: 20');
    }
}
