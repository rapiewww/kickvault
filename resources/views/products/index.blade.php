@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="fw-bold mb-0"><i class="bi bi-box-seam me-2"></i>Daftar Produk Perlengkapan Lari</h3>
    @if(auth()->user()->role === 'admin')
        <a href="{{ route('products.create') }}" class="btn btn-dark"><i class="bi bi-plus-lg me-1"></i> Tambah Produk</a>
    @endif
</div>

<div class="card border-0 shadow-sm mb-3">
    <div class="card-body">
        <form action="{{ route('products.index') }}" method="GET" class="row g-2">
            <div class="col-md-6">
                <input type="text" name="search" class="form-control" placeholder="Cari berdasarkan Nama atau SKU Produk..." value="{{ $search ?? '' }}">
            </div>
            <div class="col-md-4">
                <select name="category_id" class="form-select">
                    <option value="">-- Semua Kategori --</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ ($categoryId ?? '') == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2 d-flex gap-1">
                <button type="submit" class="btn btn-secondary w-100">Filter</button>
                @if(!empty($search) || !empty($categoryId))
                    <a href="{{ route('products.index') }}" class="btn btn-outline-secondary">Reset</a>
                @endif
            </div>
        </form>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>Produk</th>
                    <th>Kategori</th>
                    <th>Harga Beli</th>
                    <th>Harga Jual</th>
                    <th>Stok</th>
                    <th class="text-end">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($products as $product)
                <tr>
                    <td>
                        <div class="d-flex align-items-center gap-3">
                            <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="rounded-3 shadow-sm border flex-shrink-0" style="width: 48px; height: 48px; object-fit: cover;">
                            <div>
                                <div class="fw-bold text-dark">{{ $product->name }}</div>
                                <code class="small text-muted">{{ $product->sku }}</code>
                            </div>
                        </div>
                    </td>
                    <td><span class="badge bg-light text-dark border">{{ $product->category->name ?? '-' }}</span></td>
                    <td>Rp {{ number_format($product->purchase_price, 0, ',', '.') }}</td>
                    <td class="fw-semibold text-primary">Rp {{ number_format($product->selling_price, 0, ',', '.') }}</td>
                    <td>
                        <span class="badge {{ $product->stock <= $product->minimum_stock ? 'bg-danger-subtle text-danger border border-danger-subtle' : 'bg-success-subtle text-success border border-success-subtle' }} px-2 py-1">
                            {{ $product->stock }} unit
                        </span>
                    </td>
                    <td class="text-end">
                        <a href="{{ route('products.show', $product->id) }}" class="btn btn-sm btn-outline-dark">Detail</a>
                        @if(auth()->user()->role === 'admin')
                            <a href="{{ route('products.edit', $product->id) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                            <form action="{{ route('products.destroy', $product->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus produk ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger">Hapus</button>
                            </form>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center py-4 text-muted">Belum ada data produk yang sesuai.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-4">
    {{ $products->links() }}
</div>
@endsection
