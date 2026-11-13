@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="fw-bold mb-1"><i class="bi bi-file-earmark-text me-2"></i>Detail Purchase Order (Stock-In)</h3>
        <span class="text-muted">No. PO: <code>{{ $purchase->purchase_number }}</code></span>
    </div>
    <div class="d-flex gap-2">
        @if(auth()->user()->role === 'admin' && $purchase->status === 'completed')
            <form action="{{ route('purchases.cancel', $purchase->id) }}" method="POST" onsubmit="return confirm('Yakin ingin membatalkan transaksi pembelian ini? Stok barang akan dikurangi kembali.')">
                @csrf
                @method('PATCH')
                <button type="submit" class="btn btn-danger">Batalkan Pembelian</button>
            </form>
        @endif
        <a href="{{ route('purchases.index') }}" class="btn btn-secondary">Kembali</a>
    </div>
</div>

<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <div class="row">
            <div class="col-md-3">
                <small class="text-muted d-block">Nomor PO</small>
                <strong>{{ $purchase->purchase_number }}</strong>
            </div>
            <div class="col-md-3">
                <small class="text-muted d-block">Supplier</small>
                <strong>{{ $purchase->supplier->name ?? '-' }}</strong>
            </div>
            <div class="col-md-2">
                <small class="text-muted d-block">Tanggal Pembelian</small>
                <strong>{{ $purchase->purchased_at->format('d M Y') }}</strong>
            </div>
            <div class="col-md-2">
                <small class="text-muted d-block">Dicatat Oleh</small>
                <strong>{{ $purchase->user->name ?? '-' }}</strong>
            </div>
            <div class="col-md-2">
                <small class="text-muted d-block">Status</small>
                <span class="badge {{ $purchase->status === 'cancelled' ? 'bg-danger' : 'bg-success' }}">
                    {{ strtoupper($purchase->status) }}
                </span>
            </div>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-header bg-white py-3">
        <h6 class="mb-0 fw-bold">Rincian Barang Masuk</h6>
    </div>
    <div class="card-body p-0">
        <table class="table align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>SKU</th>
                    <th>Nama Produk</th>
                    <th>Kategori</th>
                    <th class="text-end">Harga Beli Satuan</th>
                    <th class="text-center">Kuantitas Masuk</th>
                    <th class="text-end">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @foreach($purchase->items as $item)
                <tr>
                    <td><code>{{ $item->product->sku ?? '-' }}</code></td>
                    <td class="fw-semibold">{{ $item->product->name ?? '-' }}</td>
                    <td>{{ $item->product->category->name ?? '-' }}</td>
                    <td class="text-end">Rp {{ number_format($item->unit_cost, 0, ',', '.') }}</td>
                    <td class="text-center">+{{ $item->quantity }} unit</td>
                    <td class="text-end fw-semibold">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot class="table-light">
                <tr>
                    <th colspan="5" class="text-end">Total Pembelian:</th>
                    <th class="text-end text-success fs-5">Rp {{ number_format($purchase->total, 0, ',', '.') }}</th>
                </tr>
            </tfoot>
        </table>
    </div>
</div>
@endsection
