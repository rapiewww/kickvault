@extends('layouts.app')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <h4 class="fw-bold mb-4">Tambah Supplier Baru</h4>

                <form action="{{ route('suppliers.store') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Nama Perusahaan / Supplier</label>
                        <input type="text" name="name" class="form-control" value="{{ old('name') }}" placeholder="Contoh: PT Official Sport" required autofocus>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">No. Telepon / WhatsApp</label>
                        <input type="text" name="phone" class="form-control" value="{{ old('phone') }}" placeholder="Contoh: 08123456789" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email (Opsional)</label>
                        <input type="email" name="email" class="form-control" value="{{ old('email') }}" placeholder="Contoh: info@sportdistributor.com">
                    </div>
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('suppliers.index') }}" class="btn btn-secondary">Kembali</a>
                        <button type="submit" class="btn btn-dark">Simpan Supplier</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
