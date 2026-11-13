@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="fw-bold mb-0"><i class="bi bi-cart-plus me-2"></i>Riwayat Pembelian (Stock-In)</h3>
    <a href="{{ route('purchases.create') }}" class="btn btn-dark"><i class="bi bi-plus-lg me-1"></i> Catat Pembelian Baru</a>
</div>

<div class="card border-0 shadow-sm mb-3">
    <div class="card-body">
        <form action="{{ route('purchases.index') }}" method="GET" class="row g-2 align-items-end">
            <div class="col-md-3">
                <label class="form-label small text-muted mb-1">No. PO</label>
                <input type="text" name="search" class="form-control" placeholder="Cari PO-..." value="{{ $search ?? '' }}">
            </div>
            <div class="col-md-3">
                <label class="form-label small text-muted mb-1">Dari Tanggal</label>
                <input type="date" name="start_date" class="form-control" value="{{ $startDate ?? '' }}">
            </div>
            <div class="col-md-3">
                <label class="form-label small text-muted mb-1">Sampai Tanggal</label>
                <input type="date" name="end_date" class="form-control" value="{{ $endDate ?? '' }}">
            </div>
            <div class="col-md-2">
                <label class="form-label small text-muted mb-1">Status</label>
                <select name="status" class="form-select">
                    <option value="">Semua Status</option>
                    <option value="completed" {{ ($status ?? '') === 'completed' ? 'selected' : '' }}>Completed</option>
                    <option value="cancelled" {{ ($status ?? '') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                </select>
            </div>
            <div class="col-md-1 d-grid">
                <button type="submit" class="btn btn-secondary">Filter</button>
            </div>
        </form>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>No. PO</th>
                    <th>Tanggal</th>
                    <th>Supplier</th>
                    <th>Petugas</th>
                    <th>Status</th>
                    <th>Total Biaya</th>
                    <th>Ringkasan Barang</th>
                    <th class="text-end">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($purchases as $purchase)
                <tr>
                    <td><code>{{ $purchase->purchase_number }}</code></td>
                    <td>{{ $purchase->purchased_at->format('d M Y') }}</td>
                    <td class="fw-semibold">{{ $purchase->supplier->name ?? '-' }}</td>
                    <td>{{ $purchase->user->name ?? '-' }}</td>
                    <td>
                        <span class="badge {{ $purchase->status === 'cancelled' ? 'bg-danger' : 'bg-success' }}">
                            {{ strtoupper($purchase->status) }}
                        </span>
                    </td>
                    <td class="text-success fw-bold">Rp {{ number_format($purchase->total, 0, ',', '.') }}</td>
                    <td>
                        <ul class="mb-0 ps-3 small">
                            @foreach($purchase->items as $item)
                                <li>{{ $item->product->name ?? 'Produk' }} (<strong>+{{ $item->quantity }}</strong> unit)</li>
                            @endforeach
                        </ul>
                    </td>
                    <td class="text-end">
                        <a href="{{ route('purchases.show', $purchase->id) }}" class="btn btn-sm btn-outline-dark">Detail</a>
                        @if(auth()->user()->role === 'admin' && $purchase->status === 'completed')
                            <form action="{{ route('purchases.cancel', $purchase->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Batalkan pembelian ini? Stok produk yang masuk akan ditarik kembali.')">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="btn btn-sm btn-outline-danger">Batalkan</button>
                            </form>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="text-center py-4 text-muted">Belum ada riwayat transaksi pembelian.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-4">
    {{ $purchases->links() }}
</div>
@endsection
