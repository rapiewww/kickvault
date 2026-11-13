@extends('layouts.app')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <h4 class="fw-bold mb-4">Edit Kategori</h4>

                <form action="{{ route('categories.update', $category->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="mb-3">
                        <label class="form-label">Nama Kategori</label>
                        <input type="text" name="name" class="form-control" value="{{ old('name', $category->name) }}" required autofocus>
                    </div>
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('categories.index') }}" class="btn btn-secondary">Kembali</a>
                        <button type="submit" class="btn btn-dark">Perbarui Kategori</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
