@extends('layouts.app')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <h4 class="fw-bold mb-4">Tambah Pengguna Baru</h4>

                <form action="{{ route('users.store') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Nama Lengkap</label>
                        <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Alamat Email</label>
                        <input type="email" name="email" class="form-control" value="{{ old('email') }}" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Password</label>
                        <input type="password" name="password" class="form-control" minlength="6" required>
                        <small class="text-muted">Minimal 6 karakter.</small>
                    </div>

                    <div class="mb-4">
                        <label class="form-label">Role / Hak Akses</label>
                        <select name="role" class="form-select" required>
                            <option value="staff" {{ old('role') === 'staff' ? 'selected' : '' }}>Staff (Kasir / Gudang)</option>
                            <option value="admin" {{ old('role') === 'admin' ? 'selected' : '' }}>Admin / Owner</option>
                            <option value="customer" {{ old('role') === 'customer' ? 'selected' : '' }}>Customer (Pelanggan)</option>
                        </select>
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('users.index') }}" class="btn btn-secondary">Kembali</a>
                        <button type="submit" class="btn btn-dark">Simpan Pengguna</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
