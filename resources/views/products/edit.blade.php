@extends('layouts.app')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <h4 class="fw-bold mb-4">Edit Produk</h4>

                <form action="{{ route('products.update', $product->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="mb-4 d-flex align-items-center gap-3 p-3 bg-light rounded-3 border">
                        <img id="image_preview" src="{{ $product->image_url }}" alt="{{ $product->name }}" class="rounded-3 shadow-sm border" style="width: 80px; height: 80px; object-fit: cover;">
                        <div>
                            <span class="badge bg-secondary mb-1">Foto Produk Saat Ini</span>
                            <div class="small text-muted">Upload file baru di bawah jika ingin mengganti gambar produk.</div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">SKU Produk</label>
                            <input type="text" name="sku" class="form-control" value="{{ old('sku', $product->sku) }}" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nama Produk</label>
                            <input type="text" name="name" class="form-control" value="{{ old('name', $product->name) }}" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Kategori</label>
                            <select name="category_id" class="form-select" required>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}" {{ old('category_id', $product->category_id) == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Ganti Foto (File Upload)</label>
                            <input type="file" name="image" id="product_image_input" class="form-control" accept="image/*">
                            <small class="text-muted">Maksimal 2MB (JPG, PNG, WEBP)</small>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Atau Masukkan URL Gambar Baru (Opsional)</label>
                        <input type="url" name="image_url_input" id="image_url_input" class="form-control" value="{{ old('image_url_input', str_starts_with($product->image ?? '', 'http') ? $product->image : '') }}" placeholder="https://images.unsplash.com/...">
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Harga Beli (Rp)</label>
                            <input type="number" name="purchase_price" class="form-control" value="{{ old('purchase_price', $product->purchase_price) }}" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Harga Jual (Rp)</label>
                            <input type="number" name="selling_price" class="form-control" value="{{ old('selling_price', $product->selling_price) }}" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Stok Saat Ini</label>
                            <input type="number" class="form-control bg-light" value="{{ $product->stock }}" readonly disabled>
                            <small class="text-muted d-block mt-1">Stok hanya dapat berubah secara otomatis melalui transaksi Purchases (Stock-In) atau Sales (Stock-Out).</small>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Minimum Stok</label>
                            <input type="number" name="minimum_stock" class="form-control" value="{{ old('minimum_stock', $product->minimum_stock) }}" required>
                        </div>
                    </div>
                    <div class="d-flex justify-content-between mt-4">
                        <a href="{{ route('products.index') }}" class="btn btn-secondary">Kembali</a>
                        <button type="submit" class="btn btn-dark">Perbarui Produk</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    const fileInput = document.getElementById('product_image_input');
    const urlInput = document.getElementById('image_url_input');
    const imagePreview = document.getElementById('image_preview');

    fileInput.addEventListener('change', function() {
        if (this.files && this.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                imagePreview.src = e.target.result;
            };
            reader.readAsDataURL(this.files[0]);
        }
    });

    urlInput.addEventListener('input', function() {
        if (this.value.trim().length > 5) {
            imagePreview.src = this.value.trim();
        }
    });
</script>
@endsection
