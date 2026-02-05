<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Division;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class UserManagementController extends Controller
{
    private function getAvailableRoles()
    {
        return ['admin', 'staff_pengelola', 'atk_master'];
    }

    public function index()
    {
        $users = User::with('division')->orderBy('name')->paginate(20);
        return view('users.index', compact('users'));
    }

    public function create()
    {
        $roles = $this->getAvailableRoles();
        $divisions = Division::orderBy('nama')->get();
        return view('users.create', compact('roles', 'divisions'));
    }

    public function store(Request $request)
    {
        $roles = $this->getAvailableRoles();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'role' => 'required|in:' . implode(',', $roles),
            'division_id' => 'nullable|exists:divisions,id',
            'password' => 'required|string|min:8|confirmed',
        ]);

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role' => $validated['role'],
            'division_id' => $validated['division_id'] ?? null,
            'password' => Hash::make($validated['password']),
        ]);

        return redirect()->route('users.index')->with('success', 'User Berhasil dibuat.');
    }

    public function edit(User $user)
    {
        $roles = $this->getAvailableRoles();
        $divisions = Division::orderBy('nama')->get();
        return view('users.edit', compact('user', 'roles', 'divisions'));
    }

    public function update(Request $request, User $user)
    {
        $roles = $this->getAvailableRoles();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'role' => 'required|in:' . implode(',', $roles),
            'division_id' => 'nullable|exists:divisions,id',
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->role = $validated['role'];
        
        if (array_key_exists('division_id', $validated)) {
            $user->division_id = $validated['division_id'];
        }

        if (!empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();

        return redirect()->route('users.index')->with('success', 'User Berhasil diperbarui.');
    }

    public function destroy(User $user)
    {
        if (Auth::id() == $user->id) {
            return redirect()->route('users.index')->with('error', 'Anda tidak dapat menghapus diri sendiri.');
        }

        $user->delete();
        return redirect()->route('users.index')->with('success', 'User Berhasil dihapus.');
    }
}
