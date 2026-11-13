@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="fw-bold mb-0"><i class="bi bi-people me-2"></i>Manajemen Pengguna & Role</h3>
    <a href="{{ route('users.create') }}" class="btn btn-dark"><i class="bi bi-plus-lg me-1"></i> Tambah Pengguna</a>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>Nama</th>
                    <th>Email</th>
                    <th>Role Saat Ini</th>
                    <th>Total Transaksi Dicatat</th>
                    <th>Ubah Role</th>
                    <th class="text-end">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $user)
                <tr>
                    <td class="fw-semibold">
                        {{ $user->name }}
                        @if($user->id === auth()->id())
                            <span class="badge bg-info text-dark ms-1">Anda</span>
                        @endif
                    </td>
                    <td>{{ $user->email }}</td>
                    <td>
                        <span class="badge {{ $user->role === 'admin' ? 'bg-primary' : ($user->role === 'customer' ? 'bg-success' : 'bg-secondary') }} text-uppercase">
                            {{ $user->role }}
                        </span>
                    </td>
                    <td>{{ $user->sales_count + $user->purchases_count }} transaksi</td>
                    <td style="max-width: 220px;">
                        @if($user->id !== auth()->id())
                            <form action="{{ route('users.updateRole', $user->id) }}" method="POST" class="d-flex gap-1">
                                @csrf
                                @method('PATCH')
                                <select name="role" class="form-select form-select-sm">
                                    <option value="staff" {{ $user->role === 'staff' ? 'selected' : '' }}>Staff</option>
                                    <option value="admin" {{ $user->role === 'admin' ? 'selected' : '' }}>Admin</option>
                                    <option value="customer" {{ $user->role === 'customer' ? 'selected' : '' }}>Customer</option>
                                </select>
                                <button type="submit" class="btn btn-sm btn-outline-primary">Simpan</button>
                            </form>
                        @else
                            <span class="text-muted small">-</span>
                        @endif
                    </td>
                    <td class="text-end">
                        @if($user->id !== auth()->id())
                            <form action="{{ route('users.destroy', $user->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus pengguna ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger">Hapus</button>
                            </form>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center py-4 text-muted">Belum ada data pengguna.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-4">
    {{ $users->links() }}
</div>
@endsection
