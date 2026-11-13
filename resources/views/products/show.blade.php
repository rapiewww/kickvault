@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="fw-bold mb-1"><i class="bi bi-box-seam me-2"></i>Detail Produk: {{ $product->name }}</h3>
        <span class="text-muted">SKU: <code>{{ $product->sku }}</code></span>
    </div>
    <div class="d-flex gap-2">
        @if(auth()->user()->role === 'admin')
            <a href="{{ route('products.edit', $product->id) }}" class="btn btn-primary">Edit Produk</a>
        @endif
        <a href="{{ route('products.index') }}" class="btn btn-secondary">Kembali</a>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-md-4">
        <div class="card border-0 shadow-sm overflow-hidden h-100">
            <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="w-100 h-100 rounded-3" style="min-height: 220px; max-height: 280px; object-fit: cover;">
        </div>
    </div>
    <div class="col-md-8">
        <div class="row g-3 h-100">
            <div class="col-sm-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <h6 class="text-muted small">Kategori</h6>
                        <h5 class="fw-bold mb-0">{{ $product->category->name ?? '-' }}</h5>
                    </div>
                </div>
            </div>
            <div class="col-sm-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <h6 class="text-muted small">Harga Beli</h6>
                        <h5 class="fw-bold mb-0">Rp {{ number_format($product->purchase_price, 0, ',', '.') }}</h5>
                    </div>
                </div>
            </div>
            <div class="col-sm-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <h6 class="text-muted small">Harga Jual</h6>
                        <h5 class="fw-bold text-primary mb-0">Rp {{ number_format($product->selling_price, 0, ',', '.') }}</h5>
                    </div>
                </div>
            </div>
            <div class="col-sm-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <h6 class="text-muted small">Stok Saat Ini (Min: {{ $product->minimum_stock }})</h6>
                        <h5 class="fw-bold mb-0">
                            <span class="badge {{ $product->stock <= $product->minimum_stock ? 'bg-danger-subtle text-danger border border-danger-subtle' : 'bg-success-subtle text-success border border-success-subtle' }} px-3 py-2">
                                {{ $product->stock }} unit
                            </span>
                        </h5>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-md-6">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3">
                <h6 class="mb-0 fw-bold"><i class="bi bi-cart-plus me-2"></i>Riwayat Pembelian (Stock-In)</h6>
            </div>
            <div class="card-body p-0">
                <table class="table table-sm table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>No. PO</th>
                            <th>Tanggal</th>
                            <th>Supplier</th>
                            <th>Qty</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($product->purchaseItems as $item)
                        <tr>
                            <td>
                                <a href="{{ route('purchases.show', $item->purchase_id) }}" class="text-decoration-none">
                                    <code>{{ $item->purchase->purchase_number ?? '-' }}</code>
                                </a>
                            </td>
                            <td>{{ $item->purchase?->purchased_at?->format('d M Y') ?? '-' }}</td>
                            <td>{{ $item->purchase->supplier->name ?? '-' }}</td>
                            <td class="text-success fw-bold">+{{ $item->quantity }}</td>
                            <td>
                                <span class="badge {{ ($item->purchase->status ?? '') === 'cancelled' ? 'bg-danger' : 'bg-success' }}">
                                    {{ strtoupper($item->purchase->status ?? '-') }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-3 text-muted">Belum ada riwayat pembelian.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3">
                <h6 class="mb-0 fw-bold"><i class="bi bi-receipt me-2"></i>Riwayat Penjualan (Stock-Out)</h6>
            </div>
            <div class="card-body p-0">
                <table class="table table-sm table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>No. Invoice</th>
                            <th>Tanggal</th>
                            <th>Kasir</th>
                            <th>Qty</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($product->saleItems as $item)
                        <tr>
                            <td>
                                <a href="{{ route('sales.show', $item->sale_id) }}" class="text-decoration-none">
                                    <code>{{ $item->sale->sale_number ?? '-' }}</code>
                                </a>
                            </td>
                            <td>{{ $item->sale?->sold_at?->format('d M Y') ?? '-' }}</td>
                            <td>{{ $item->sale->user->name ?? '-' }}</td>
                            <td class="text-danger fw-bold">-{{ $item->quantity }}</td>
                            <td>
                                <span class="badge {{ ($item->sale->status ?? '') === 'cancelled' ? 'bg-danger' : 'bg-success' }}">
                                    {{ strtoupper($item->sale->status ?? '-') }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-3 text-muted">Belum ada riwayat penjualan.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
