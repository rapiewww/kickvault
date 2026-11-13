@extends('layouts.app')

@section('content')
<div class="row justify-content-center mt-4">
    <div class="col-md-5">
        <div class="card shadow-sm border-0 rounded-4">
            <div class="card-body p-4 p-md-5">
                <div class="text-center mb-4">
                    <div class="rounded-circle bg-dark text-white d-inline-flex align-items-center justify-content-center mb-2 shadow-sm" style="width: 52px; height: 52px;">
                        <i class="bi bi-lightning-charge-fill fs-4"></i>
                    </div>
                    <h4 class="fw-bold text-dark mb-1">RunnersVault</h4>
                    <p class="text-muted small mb-0">Inventory & Point of Sale System</p>
                </div>

                <form action="{{ route('login.post') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Email Address</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light"><i class="bi bi-envelope"></i></span>
                            <input type="email" name="email" id="login-email" class="form-control" value="{{ old('email') }}" required autofocus placeholder="admin@runnersvault.com">
                        </div>
                    </div>
                    <div class="mb-4">
                        <label class="form-label small fw-semibold">Password</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light"><i class="bi bi-lock"></i></span>
                            <input type="password" name="password" id="login-password" class="form-control" required placeholder="••••••••">
                        </div>
                    </div>
                    <button type="submit" class="btn btn-pos-dark w-100 py-2 fw-semibold shadow-sm">
                        <i class="bi bi-box-arrow-in-right me-1"></i> Login
                    </button>
                </form>

                <div class="text-center mt-3">
                    <span class="text-muted small">Belum punya akun pelanggan?</span>
                    <a href="{{ route('register') }}" class="text-decoration-none small fw-semibold ms-1">Daftar di sini</a>
                </div>

                <hr class="my-4">

                <!-- Quick-Fill Akun Demo -->
                <div class="p-3 bg-light rounded-3 border">
                    <div class="d-flex align-items-center gap-1 mb-2">
                        <i class="bi bi-magic text-primary"></i>
                        <span class="small fw-bold text-dark">Akun Demo (Klik untuk isi otomatis):</span>
                    </div>
                    <div class="d-grid gap-2">
                        <button type="button" class="btn btn-sm btn-outline-dark text-start d-flex justify-content-between align-items-center py-2" onclick="fillLogin('admin@runnersvault.com', 'password')">
                            <span><strong class="d-block text-dark">Admin / Owner</strong> <code class="small text-muted">admin@runnersvault.com</code></span>
                            <span class="badge bg-primary text-uppercase">Admin</span>
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-dark text-start d-flex justify-content-between align-items-center py-2" onclick="fillLogin('staff@runnersvault.com', 'password')">
                            <span><strong class="d-block text-dark">Staff Kasir</strong> <code class="small text-muted">staff@runnersvault.com</code></span>
                            <span class="badge bg-secondary text-uppercase">Staff</span>
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-dark text-start d-flex justify-content-between align-items-center py-2" onclick="fillLogin('customer@runnersvault.com', 'password')">
                            <span><strong class="d-block text-dark">Customer</strong> <code class="small text-muted">customer@runnersvault.com</code></span>
                            <span class="badge bg-success text-uppercase">Customer</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function fillLogin(email, password) {
        const emailInput = document.getElementById('login-email');
        const passInput = document.getElementById('login-password');
        if (emailInput && passInput) {
            emailInput.value = email;
            passInput.value = password;
            emailInput.focus();
        }
    }
</script>
@endsection
