@extends('layouts.app')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <h4 class="fw-bold mb-4">Tambah Produk Baru</h4>

                <form action="{{ route('products.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">SKU Produk</label>
                            <input type="text" name="sku" class="form-control" value="{{ old('sku') }}" placeholder="Contoh: SHO-ORT-01" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nama Produk</label>
                            <input type="text" name="name" class="form-control" value="{{ old('name') }}" placeholder="Contoh: Ortuseight Hyperblast" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Kategori</label>
                            <select name="category_id" class="form-select" required>
                                <option value="">-- Pilih Kategori --</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Foto Produk (File Upload)</label>
                            <input type="file" name="image" id="product_image_input" class="form-control" accept="image/*">
                            <small class="text-muted">Maksimal 2MB (JPG, PNG, WEBP)</small>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Atau URL Gambar Eksternal (Opsional)</label>
                        <input type="url" name="image_url_input" id="image_url_input" class="form-control" value="{{ old('image_url_input') }}" placeholder="https://images.unsplash.com/...">
                    </div>

                    <div class="mb-3 text-center d-none" id="preview_container">
                        <label class="form-label d-block text-start small text-muted">Preview Gambar:</label>
                        <img id="image_preview" src="" alt="Preview" class="rounded-3 border shadow-sm" style="max-height: 180px; object-fit: cover;">
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Harga Beli (Rp)</label>
                            <input type="number" name="purchase_price" class="form-control" value="{{ old('purchase_price') }}" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Harga Jual (Rp)</label>
                            <input type="number" name="selling_price" class="form-control" value="{{ old('selling_price') }}" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Stok Awal</label>
                            <input type="number" name="stock" class="form-control" value="{{ old('stock', 0) }}" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Minimum Stok (Peringatan)</label>
                            <input type="number" name="minimum_stock" class="form-control" value="{{ old('minimum_stock', 5) }}" required>
                        </div>
                    </div>
                    <div class="d-flex justify-content-between mt-4">
                        <a href="{{ route('products.index') }}" class="btn btn-secondary">Kembali</a>
                        <button type="submit" class="btn btn-dark">Simpan Produk</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    const fileInput = document.getElementById('product_image_input');
    const urlInput = document.getElementById('image_url_input');
    const previewContainer = document.getElementById('preview_container');
    const imagePreview = document.getElementById('image_preview');

    fileInput.addEventListener('change', function() {
        if (this.files && this.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                imagePreview.src = e.target.result;
                previewContainer.classList.remove('d-none');
            };
            reader.readAsDataURL(this.files[0]);
        }
    });

    urlInput.addEventListener('input', function() {
        if (this.value.trim().length > 5) {
            imagePreview.src = this.value.trim();
            previewContainer.classList.remove('d-none');
        }
    });
</script>
@endsection
