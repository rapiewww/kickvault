@extends('layouts.app')

@section('content')
<div class="row justify-content-center mt-4">
    <div class="col-md-5">
        <div class="card shadow-sm border-0">
            <div class="card-body p-4">
                <div class="text-center mb-4">
                    <div class="rounded-circle bg-dark text-white d-inline-flex align-items-center justify-content-center mb-2 shadow-sm" style="width: 52px; height: 52px;">
                        <i class="bi bi-person-plus-fill fs-4"></i>
                    </div>
                    <h4 class="fw-bold text-dark mb-1">RunnersVault</h4>
                    <p class="text-muted small mb-0">Daftar Akun Baru Pelanggan (Customer)</p>
                </div>

                <form action="{{ route('register.post') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Nama Lengkap</label>
                        <input type="text" name="name" class="form-control" value="{{ old('name') }}" required autofocus placeholder="Contoh: Budi Runner">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Alamat Email</label>
                        <input type="email" name="email" class="form-control" value="{{ old('email') }}" required placeholder="budi@example.com">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Password</label>
                        <input type="password" name="password" class="form-control" minlength="6" required placeholder="Minimal 6 karakter">
                    </div>
                    <div class="mb-4">
                        <label class="form-label">Konfirmasi Password</label>
                        <input type="password" name="password_confirmation" class="form-control" minlength="6" required placeholder="Ulangi password">
                    </div>
                    <button type="submit" class="btn btn-dark w-100 py-2">Daftar Sebagai Pelanggan</button>
                </form>

                <hr class="my-4">
                <div class="text-center">
                    <span class="text-muted small">Sudah memiliki akun?</span>
                    <a href="{{ route('login') }}" class="text-decoration-none small fw-semibold ms-1">Masuk di sini</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
