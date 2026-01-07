<?php

namespace Tests\Feature;

use App\Models\AtkRequest;
use App\Models\AtkRequestItem;
use App\Models\Division;
use App\Models\Item;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AtkRequestWorkflowTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Division $division;
    protected Item $requestableItem;
    protected Item $nonRequestableItem;

    protected function setUp(): void
    {
        parent::setUp();

        // Create test data
        $this->division = Division::create([
            'nama' => 'IT Department',
            'kode' => 'IT',
        ]);

        $this->user = User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
            'role' => 'staff_pengelola',
            'division_id' => $this->division->id,
        ]);

        $this->requestableItem = Item::create([
            'kode_barang' => 'ATK-001',
            'nama_barang' => 'Pulpen Hitam',
            'satuan' => 'pcs',
            'stok_awal' => 100,
            'stok_terkini' => 100,
            'is_requestable' => true,
        ]);

        $this->nonRequestableItem = Item::create([
            'kode_barang' => 'ATK-002',
            'nama_barang' => 'Printer',
            'satuan' => 'unit',
            'stok_awal' => 5,
            'stok_terkini' => 5,
            'is_requestable' => false,
        ]);
    }

    public function test_catalog_shows_only_requestable_items(): void
    {
        $response = $this->actingAs($this->user)->get(route('atk.catalog'));

        $response->assertStatus(200);
        $response->assertSee($this->requestableItem->nama_barang);
        $response->assertDontSee($this->nonRequestableItem->nama_barang);
    }

    public function test_catalog_requires_authentication(): void
    {
        $response = $this->get(route('atk.catalog'));
        $response->assertRedirect(route('login'));
    }

    public function test_catalog_requires_staff_pengelola_role(): void
    {
        $unauthorizedUser = User::create([
            'name' => 'Unauthorized User',
            'email' => 'unauthorized@example.com',
            'password' => bcrypt('password'),
            'role' => 'other_role',
        ]);

        $response = $this->actingAs($unauthorizedUser)->get(route('atk.catalog'));
        $response->assertStatus(403);
    }

    public function test_can_add_item_to_cart(): void
    {
        $response = $this->actingAs($this->user)->post(route('atk.cart.add'), [
            'item_id' => $this->requestableItem->id,
            'qty' => 5,
        ]);

        $response->assertRedirect(route('atk.cart'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('atk_requests', [
            'requested_by' => $this->user->id,
            'period' => now()->format('Y-m'),
            'status' => 'draft',
        ]);

        $this->assertDatabaseHas('atk_request_items', [
            'item_id' => $this->requestableItem->id,
            'qty' => 5,
        ]);
    }

    public function test_adding_same_item_increments_quantity(): void
    {
        // Add item first time
        $this->actingAs($this->user)->post(route('atk.cart.add'), [
            'item_id' => $this->requestableItem->id,
            'qty' => 3,
        ]);

        // Add same item again
        $this->actingAs($this->user)->post(route('atk.cart.add'), [
            'item_id' => $this->requestableItem->id,
            'qty' => 2,
        ]);

        $atkRequest = AtkRequest::where('requested_by', $this->user->id)
            ->where('status', 'draft')
            ->first();

        $requestItem = AtkRequestItem::where('atk_request_id', $atkRequest->id)
            ->where('item_id', $this->requestableItem->id)
            ->first();

        $this->assertEquals(5, $requestItem->qty);
    }

    public function test_cannot_add_non_requestable_item_to_cart(): void
    {
        $response = $this->actingAs($this->user)->post(route('atk.cart.add'), [
            'item_id' => $this->nonRequestableItem->id,
            'qty' => 1,
        ]);

        $response->assertSessionHas('error');
    }

    public function test_can_view_cart(): void
    {
        // Add item to cart
        $this->actingAs($this->user)->post(route('atk.cart.add'), [
            'item_id' => $this->requestableItem->id,
            'qty' => 3,
        ]);

        $response = $this->actingAs($this->user)->get(route('atk.cart'));

        $response->assertStatus(200);
        $response->assertSee($this->requestableItem->nama_barang);
        $response->assertSee('3');
    }

    public function test_can_update_cart_item_quantity(): void
    {
        // Add item to cart
        $this->actingAs($this->user)->post(route('atk.cart.add'), [
            'item_id' => $this->requestableItem->id,
            'qty' => 3,
        ]);

        $requestItem = AtkRequestItem::first();

        $response = $this->actingAs($this->user)->patch(route('atk.cart.update', $requestItem), [
            'qty' => 7,
        ]);

        $response->assertRedirect(route('atk.cart'));
        $this->assertDatabaseHas('atk_request_items', [
            'id' => $requestItem->id,
            'qty' => 7,
        ]);
    }

    public function test_can_remove_item_from_cart(): void
    {
        // Add item to cart
        $this->actingAs($this->user)->post(route('atk.cart.add'), [
            'item_id' => $this->requestableItem->id,
            'qty' => 3,
        ]);

        $requestItem = AtkRequestItem::first();

        $response = $this->actingAs($this->user)->delete(route('atk.cart.remove', $requestItem));

        $response->assertRedirect(route('atk.cart'));
        $this->assertDatabaseMissing('atk_request_items', [
            'id' => $requestItem->id,
        ]);
    }

    public function test_cannot_modify_other_users_cart(): void
    {
        // Create another user
        $otherUser = User::create([
            'name' => 'Other User',
            'email' => 'other@example.com',
            'password' => bcrypt('password'),
            'role' => 'staff_pengelola',
            'division_id' => $this->division->id,
        ]);

        // Add item to other user's cart
        $this->actingAs($otherUser)->post(route('atk.cart.add'), [
            'item_id' => $this->requestableItem->id,
            'qty' => 3,
        ]);

        $requestItem = AtkRequestItem::first();

        // Try to update with current user
        $response = $this->actingAs($this->user)->patch(route('atk.cart.update', $requestItem), [
            'qty' => 10,
        ]);

        $response->assertStatus(403);
    }

    public function test_can_checkout_cart(): void
    {
        // Add item to cart
        $this->actingAs($this->user)->post(route('atk.cart.add'), [
            'item_id' => $this->requestableItem->id,
            'qty' => 3,
        ]);

        $response = $this->actingAs($this->user)->post(route('atk.checkout'));

        $response->assertRedirect(route('atk.my-requests'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('atk_requests', [
            'requested_by' => $this->user->id,
            'status' => 'submitted',
        ]);

        $atkRequest = AtkRequest::where('requested_by', $this->user->id)
            ->where('status', 'submitted')
            ->first();

        $this->assertNotNull($atkRequest->request_number);
        $this->assertNotNull($atkRequest->submitted_at);
    }

    public function test_cannot_checkout_empty_cart(): void
    {
        $response = $this->actingAs($this->user)->post(route('atk.checkout'));

        $response->assertRedirect(route('atk.cart'));
        $response->assertSessionHas('error');
    }

    public function test_can_view_my_requests(): void
    {
        // Create a submitted request
        $atkRequest = AtkRequest::create([
            'request_number' => 'REQ-202601-0001',
            'period' => now()->format('Y-m'),
            'requested_by' => $this->user->id,
            'division_id' => $this->division->id,
            'status' => 'submitted',
            'submitted_at' => now(),
        ]);

        AtkRequestItem::create([
            'atk_request_id' => $atkRequest->id,
            'item_id' => $this->requestableItem->id,
            'qty' => 5,
        ]);

        $response = $this->actingAs($this->user)->get(route('atk.my-requests'));

        $response->assertStatus(200);
        $response->assertSee('REQ-202601-0001');
    }

    public function test_can_view_request_detail(): void
    {
        // Create a submitted request
        $atkRequest = AtkRequest::create([
            'request_number' => 'REQ-202601-0001',
            'period' => now()->format('Y-m'),
            'requested_by' => $this->user->id,
            'division_id' => $this->division->id,
            'status' => 'submitted',
            'submitted_at' => now(),
        ]);

        AtkRequestItem::create([
            'atk_request_id' => $atkRequest->id,
            'item_id' => $this->requestableItem->id,
            'qty' => 5,
        ]);

        $response = $this->actingAs($this->user)->get(route('atk.show', $atkRequest));

        $response->assertStatus(200);
        $response->assertSee('REQ-202601-0001');
        $response->assertSee($this->requestableItem->nama_barang);
        $response->assertSee('5');
    }

    public function test_cannot_view_other_users_request(): void
    {
        // Create another user
        $otherUser = User::create([
            'name' => 'Other User',
            'email' => 'other@example.com',
            'password' => bcrypt('password'),
            'role' => 'staff_pengelola',
            'division_id' => $this->division->id,
        ]);

        // Create a request for other user
        $atkRequest = AtkRequest::create([
            'request_number' => 'REQ-202601-0001',
            'period' => now()->format('Y-m'),
            'requested_by' => $otherUser->id,
            'division_id' => $this->division->id,
            'status' => 'submitted',
            'submitted_at' => now(),
        ]);

        // Try to view with current user
        $response = $this->actingAs($this->user)->get(route('atk.show', $atkRequest));

        $response->assertStatus(403);
    }

    public function test_request_number_is_sequential(): void
    {
        // Create first request
        $this->actingAs($this->user)->post(route('atk.cart.add'), [
            'item_id' => $this->requestableItem->id,
            'qty' => 1,
        ]);
        $this->actingAs($this->user)->post(route('atk.checkout'));

        $firstRequest = AtkRequest::where('status', 'submitted')->first();
        $expectedPrefix = 'REQ-' . now()->format('Ym');
        $this->assertStringStartsWith($expectedPrefix, $firstRequest->request_number);

        // Create another user and request
        $otherUser = User::create([
            'name' => 'Other User',
            'email' => 'other@example.com',
            'password' => bcrypt('password'),
            'role' => 'staff_pengelola',
            'division_id' => $this->division->id,
        ]);

        $this->actingAs($otherUser)->post(route('atk.cart.add'), [
            'item_id' => $this->requestableItem->id,
            'qty' => 1,
        ]);
        $this->actingAs($otherUser)->post(route('atk.checkout'));

        $secondRequest = AtkRequest::where('status', 'submitted')
            ->where('requested_by', $otherUser->id)
            ->first();

        // Extract sequence numbers
        $firstSequence = (int) substr($firstRequest->request_number, -4);
        $secondSequence = (int) substr($secondRequest->request_number, -4);

        $this->assertEquals($firstSequence + 1, $secondSequence);
    }

    public function test_one_draft_per_user_per_period(): void
    {
        // Add item to cart
        $this->actingAs($this->user)->post(route('atk.cart.add'), [
            'item_id' => $this->requestableItem->id,
            'qty' => 3,
        ]);

        // Add another item - should use same draft
        $secondItem = Item::create([
            'kode_barang' => 'ATK-003',
            'nama_barang' => 'Pensil',
            'satuan' => 'pcs',
            'stok_awal' => 50,
            'stok_terkini' => 50,
            'is_requestable' => true,
        ]);

        $this->actingAs($this->user)->post(route('atk.cart.add'), [
            'item_id' => $secondItem->id,
            'qty' => 2,
        ]);

        // Should only have one draft request
        $drafts = AtkRequest::where('requested_by', $this->user->id)
            ->where('period', now()->format('Y-m'))
            ->where('status', 'draft')
            ->get();

        $this->assertCount(1, $drafts);
        $this->assertCount(2, $drafts->first()->items);
    }
}
