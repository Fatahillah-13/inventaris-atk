<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\AtkShopRequest;
use App\Models\AtkShopRequestItem;
use App\Models\Item;
use App\Models\Division;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AtkPhase2TestSeeder extends Seeder
{
    /**
     * Run the Phase 2 test database seeds.
     */
    public function run(): void
    {
        // Create ATK Master user
        $atkMaster = User::firstOrCreate(
            ['email' => 'atk.master@inventaris.test'],
            [
                'name' => 'ATK Master',
                'password' => Hash::make('password'),
                'role' => 'atk_master',
                'division_id' => null,
            ]
        );

        $this->command->info('Phase 2 test user created:');
        $this->command->info('  - atk.master@inventaris.test (password: password) - ATK Master');

        // Create sample submitted requests for testing approval workflow
        $itStaff = User::where('email', 'staff.it@inventaris.test')->first();
        $hrStaff = User::where('email', 'staff.hr@inventaris.test')->first();
        $itDivision = Division::where('kode', 'IT')->first();
        $hrDivision = Division::where('kode', 'HR')->first();

        if ($itStaff && $itDivision) {
            // Create a submitted request from IT Staff
            $request1 = AtkShopRequest::create([
                'request_number' => 'REQ-' . now()->format('Ym') . '-0001',
                'period' => now()->format('Y-m'),
                'division_id' => $itDivision->id,
                'requested_by' => $itStaff->id,
                'status' => 'submitted',
                'submitted_at' => now()->subDays(2),
            ]);

            // Add items to the request
            $items = Item::where('is_requestable', true)->take(5)->get();
            foreach ($items as $index => $item) {
                AtkShopRequestItem::create([
                    'atk_shop_request_id' => $request1->id,
                    'item_id' => $item->id,
                    'qty' => ($index + 1) * 2,
                ]);
            }

            $this->command->info('Created sample submitted request: ' . $request1->request_number);
        }

        if ($hrStaff && $hrDivision) {
            // Create another submitted request from HR Staff
            $request2 = AtkShopRequest::create([
                'request_number' => 'REQ-' . now()->format('Ym') . '-0002',
                'period' => now()->format('Y-m'),
                'division_id' => $hrDivision->id,
                'requested_by' => $hrStaff->id,
                'status' => 'submitted',
                'submitted_at' => now()->subDays(1),
            ]);

            // Add items to the request
            $items = Item::where('is_requestable', true)->skip(5)->take(4)->get();
            foreach ($items as $index => $item) {
                AtkShopRequestItem::create([
                    'atk_shop_request_id' => $request2->id,
                    'item_id' => $item->id,
                    'qty' => ($index + 1) * 3,
                ]);
            }

            $this->command->info('Created sample submitted request: ' . $request2->request_number);
        }

        $this->command->info('Phase 2 test data seeded successfully!');
    }
}

