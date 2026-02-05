<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Division;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->create(['role' => 'admin']);
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
        $originalPassword = 'original_password';
        $user = User::factory()->create([
            'role' => 'staff_pengelola',
            'password' => \Illuminate\Support\Facades\Hash::make($originalPassword),
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
        $this->assertTrue(\Illuminate\Support\Facades\Hash::check($originalPassword, $user->password));
    }
}
