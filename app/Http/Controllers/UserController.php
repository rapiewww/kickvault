<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        $users = User::withCount(['sales', 'purchases'])
            ->latest()
            ->paginate(10);

        return view('users.index', compact('users'));
    }

    public function create()
    {
        return view('users.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:150', 'unique:users,email'],
            'password' => ['required', 'string', 'min:6'],
            'role' => ['required', 'in:admin,staff,customer'],
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
        ]);

        return redirect()->route('users.index')->with('success', 'Pengguna baru berhasil ditambahkan!');
    }

    public function updateRole(Request $request, User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Anda tidak dapat mengubah role akun Anda sendiri yang sedang aktif!');
        }

        $request->validate([
            'role' => ['required', 'in:admin,staff,customer'],
        ]);

        $user->update(['role' => $request->role]);

        return back()->with('success', "Role untuk {$user->name} berhasil diperbarui menjadi " . strtoupper($request->role) . ".");
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Anda tidak dapat menghapus akun Anda sendiri!');
        }

        if ($user->sales()->exists() || $user->purchases()->exists()) {
            return back()->with('error', 'Pengguna tidak dapat dihapus karena sudah memiliki riwayat pencatatan transaksi!');
        }

        $user->delete();

        return redirect()->route('users.index')->with('success', 'Pengguna berhasil dihapus.');
    }
}
