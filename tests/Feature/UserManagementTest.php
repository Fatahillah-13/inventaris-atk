<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Division;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class UserManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->create(['role' => 'admin']);
    }

    private function createTestDivision(string $nama = 'IT', string $kode = 'IT'): Division
    {
        return Division::create(['nama' => $nama, 'kode' => $kode]);
    }

    private function getNewUserData(Division $division): array
    {
        return [
            'name' => 'New User',
            'email' => 'newuser@example.com',
            'role' => 'staff_pengelola',
            'division_id' => $division->id,
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ];
    }

    /** @test */
    public function admin_can_see_user_list_with_division()
    {
        $division = Division::create(['nama' => 'IT Department', 'kode' => 'IT']);
        User::factory()->create([
            'name' => 'John Doe',
            'division_id' => $division->id
        ]);

        $response = $this->actingAs($this->admin)->get(route('users.index'));

        $response->assertStatus(200);
        $response->assertSee('John Doe');
        $response->assertSee('IT Department');
    }

    /** @test */
    public function admin_can_create_user_with_division_and_atk_master_role()
    {
        $division = Division::create(['nama' => 'Warehouse', 'kode' => 'WH']);

        $response = $this->actingAs($this->admin)->post(route('users.store'), [
            'name' => 'Jane Smith',
            'email' => 'jane@example.com',
            'role' => 'atk_master',
            'division_id' => $division->id,
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertRedirect(route('users.index'));
        $this->assertDatabaseHas('users', [
            'name' => 'Jane Smith',
            'email' => 'jane@example.com',
            'role' => 'atk_master',
            'division_id' => $division->id,
        ]);
    }

    /** @test */
    public function admin_can_update_user_division_and_role()
    {
        $user = User::factory()->create(['role' => 'staff_pengelola']);
        $division = Division::create(['nama' => 'HR', 'kode' => 'HR']);

        $response = $this->actingAs($this->admin)->put(route('users.update', $user), [
            'name' => 'Updated Name',
            'email' => $user->email,
            'role' => 'admin',
            'division_id' => $division->id,
        ]);

        $response->assertRedirect(route('users.index'));
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'Updated Name',
            'role' => 'admin',
            'division_id' => $division->id,
        ]);
    }

    /** @test */
    public function updating_user_without_password_preserves_existing_password()
    {
        $originalPassword = 'SecurePassword123!';
        $user = User::factory()->create([
            'role' => 'staff_pengelola',
            'password' => Hash::make($originalPassword),
        ]);
        $division = Division::create(['nama' => 'Finance', 'kode' => 'FIN']);

        // Store the original password hash
        $originalPasswordHash = $user->password;

        // Update user without providing password field
        $response = $this->actingAs($this->admin)->put(route('users.update', $user), [
            'name' => 'Updated Name',
            'email' => $user->email,
            'role' => 'admin',
            'division_id' => $division->id,
        ]);

        $response->assertRedirect(route('users.index'));
        
        // Verify the user was updated
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'Updated Name',
            'role' => 'admin',
            'division_id' => $division->id,
        ]);

        // Verify the password hash remains unchanged
        $user->refresh();
        $this->assertEquals($originalPasswordHash, $user->password);
        
        // Verify the original password still works
        $this->assertTrue(Hash::check($originalPassword, $user->password));
    public function admin_can_create_user_without_division()
    {
        $response = $this->actingAs($this->admin)->post(route('users.store'), [
            'name' => 'No Division User',
            'email' => 'nodivision@example.com',
            'role' => 'staff_pengelola',
            'division_id' => null,
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertRedirect(route('users.index'));
        $this->assertDatabaseHas('users', [
            'name' => 'No Division User',
            'email' => 'nodivision@example.com',
            'role' => 'staff_pengelola',
            'division_id' => null,
    public function staff_pengelola_cannot_access_user_index()
    {
        $staff = User::factory()->create(['role' => 'staff_pengelola']);

        $response = $this->actingAs($staff)->get(route('users.index'));

        $response->assertStatus(403);
    }

    /** @test */
    public function staff_pengelola_cannot_access_user_create()
    {
        $staff = User::factory()->create(['role' => 'staff_pengelola']);

        $response = $this->actingAs($staff)->get(route('users.create'));

        $response->assertStatus(403);
    }

    /** @test */
    public function staff_pengelola_cannot_create_user()
    {
        $staff = User::factory()->create(['role' => 'staff_pengelola']);
        $division = $this->createTestDivision();

        $response = $this->actingAs($staff)->post(route('users.store'), $this->getNewUserData($division));

        $response->assertStatus(403);
        $this->assertDatabaseMissing('users', [
            'email' => 'newuser@example.com',
        ]);
    }

    /** @test */
    public function admin_can_create_user_without_providing_division_id()
    {
        $response = $this->actingAs($this->admin)->post(route('users.store'), [
            'name' => 'Missing Division User',
            'email' => 'missingdivision@example.com',
            'role' => 'admin',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertRedirect(route('users.index'));
        $this->assertDatabaseHas('users', [
            'name' => 'Missing Division User',
            'email' => 'missingdivision@example.com',
            'role' => 'admin',
            'division_id' => null,
    public function staff_pengelola_cannot_access_user_edit()
    {
        $staff = User::factory()->create(['role' => 'staff_pengelola']);
        $user = User::factory()->create();

        $response = $this->actingAs($staff)->get(route('users.edit', $user));

        $response->assertStatus(403);
    }

    /** @test */
    public function staff_pengelola_cannot_update_user()
    {
        $staff = User::factory()->create(['role' => 'staff_pengelola']);
        $user = User::factory()->create(['name' => 'Original Name']);

        $response = $this->actingAs($staff)->put(route('users.update', $user), [
            'name' => 'Modified Name',
            'email' => $user->email,
            'role' => 'admin',
        ]);

        $response->assertStatus(403);
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'Original Name',
        ]);
    }

    /** @test */
    public function admin_can_update_user_to_remove_division()
    {
        $division = Division::create(['nama' => 'Old Department', 'kode' => 'OLD']);
        $user = User::factory()->create([
            'role' => 'staff_pengelola',
            'division_id' => $division->id
        ]);

        $response = $this->actingAs($this->admin)->put(route('users.update', $user), [
            'name' => $user->name,
            'email' => $user->email,
            'role' => 'staff_pengelola',
            'division_id' => null,
        ]);

        $response->assertRedirect(route('users.index'));
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'division_id' => null,
    public function staff_pengelola_cannot_delete_user()
    {
        $staff = User::factory()->create(['role' => 'staff_pengelola']);
        $user = User::factory()->create();

        $response = $this->actingAs($staff)->delete(route('users.destroy', $user));

        $response->assertStatus(403);
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
        ]);
    }

    /** @test */
    public function admin_can_update_user_without_providing_division_id()
    {
        $division = Division::create(['nama' => 'Initial Department', 'kode' => 'INIT']);
        $user = User::factory()->create([
            'role' => 'atk_master',
            'division_id' => $division->id
        ]);

        $response = $this->actingAs($this->admin)->put(route('users.update', $user), [
            'name' => 'Updated Without Division',
            'email' => $user->email,
            'role' => 'atk_master',
        ]);

        $response->assertRedirect(route('users.index'));
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'Updated Without Division',
            'division_id' => $division->id, // Should preserve existing division
    public function atk_master_cannot_access_user_index()
    {
        $atkMaster = User::factory()->create(['role' => 'atk_master']);

        $response = $this->actingAs($atkMaster)->get(route('users.index'));

        $response->assertStatus(403);
    }

    /** @test */
    public function atk_master_cannot_access_user_create()
    {
        $atkMaster = User::factory()->create(['role' => 'atk_master']);

        $response = $this->actingAs($atkMaster)->get(route('users.create'));

        $response->assertStatus(403);
    }

    /** @test */
    public function atk_master_cannot_create_user()
    {
        $atkMaster = User::factory()->create(['role' => 'atk_master']);
        $division = $this->createTestDivision();

        $response = $this->actingAs($atkMaster)->post(route('users.store'), $this->getNewUserData($division));

        $response->assertStatus(403);
        $this->assertDatabaseMissing('users', [
            'email' => 'newuser@example.com',
        ]);
    }

    /** @test */
    public function atk_master_cannot_access_user_edit()
    {
        $atkMaster = User::factory()->create(['role' => 'atk_master']);
        $user = User::factory()->create();

        $response = $this->actingAs($atkMaster)->get(route('users.edit', $user));

        $response->assertStatus(403);
    }

    /** @test */
    public function atk_master_cannot_update_user()
    {
        $atkMaster = User::factory()->create(['role' => 'atk_master']);
        $user = User::factory()->create(['name' => 'Original Name']);

        $response = $this->actingAs($atkMaster)->put(route('users.update', $user), [
            'name' => 'Modified Name',
            'email' => $user->email,
            'role' => 'admin',
        ]);

        $response->assertStatus(403);
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'Original Name',
        ]);
    }

    /** @test */
    public function atk_master_cannot_delete_user()
    {
        $atkMaster = User::factory()->create(['role' => 'atk_master']);
        $user = User::factory()->create();

        $response = $this->actingAs($atkMaster)->delete(route('users.destroy', $user));

        $response->assertStatus(403);
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
        ]);
    }
}
