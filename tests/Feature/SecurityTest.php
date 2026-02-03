<?php

namespace Tests\Feature;

use App\Models\Item;
use App\Models\User;
use App\Models\Division;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SecurityTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function employee_by_nik_route_should_be_throttled()
    {
        // This is a bit hard to test in a standard way without external tools,
        // but we can at least check if the middleware is assigned.
        $route = \Route::getRoutes()->getByName('ajax.employee.byNik');
        $this->assertNotNull($route);
        $this->assertContains('throttle:10,1', $route->middleware());
    }

    /** @test */
    public function public_loan_store_handles_non_loanable_item_gracefully()
    {
        $division = Division::create(['nama' => 'Test Division', 'kode' => 'TD']);
        $item = Item::create([
            'kode_barang' => 'ITEM001',
            'nama_barang' => 'Test Item',
            'satuan' => 'pcs',
            'can_be_loaned' => false,
        ]);

        $response = $this->post(route('public.loans.store'), [
            'item_id' => $item->id,
            'division_id' => $division->id,
            'employee_id' => '12345',
            'jumlah' => 1,
            'tanggal_pinjam' => now()->toDateString(),
        ]);

        // If it crashes, it will return 500. We want a 400 or a redirect with error.
        $this->assertNotEquals(500, $response->getStatusCode());
    }
}
