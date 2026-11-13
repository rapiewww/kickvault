@extends('layouts.app')

@section('content')
<style>
@media print {
    /* Sembunyikan elemen navigasi dan kontrol browser */
    .sidebar,
    .topbar,
    .btn,
    .no-print,
    header,
    nav {
        display: none !important;
    }

    body {
        background: #ffffff !important;
        color: #000000 !important;
        font-family: Arial, sans-serif !important;
        margin: 0 !important;
        padding: 0 !important;
    }

    .main-wrapper {
        margin: 0 !important;
        padding: 0 !important;
        width: 100% !important;
    }

    .content-area {
        padding: 0 !important;
    }

    .card {
        border: 1px solid #e2e8f0 !important;
        box-shadow: none !important;
        margin-bottom: 1.5rem !important;
        page-break-inside: avoid;
    }

    .print-only {
        display: block !important;
    }

    .table {
        color: #000 !important;
    }

    .badge {
        border: 1px solid #999 !important;
        color: #000 !important;
        background: transparent !important;
    }
}
@media screen {
    .print-only {
        display: none !important;
    }
}
</style>

<div class="d-flex justify-content-between align-items-center mb-4 no-print">
    <div>
        <h3 class="fw-bold mb-1"><i class="bi bi-receipt me-2"></i>Detail Nota Penjualan</h3>
        <span class="text-muted">No. Invoice: <code>{{ $sale->sale_number }}</code></span>
    </div>
    <div class="d-flex gap-2">
        <button type="button" onclick="window.print()" class="btn btn-dark">
            <i class="bi bi-printer me-1"></i> Cetak Struk
        </button>
        @if(auth()->user()->role === 'admin' && $sale->status === 'completed')
            <form action="{{ route('sales.cancel', $sale->id) }}" method="POST" onsubmit="return confirm('Yakin ingin membatalkan transaksi ini? Stok barang akan dikembalikan.')">
                @csrf
                @method('PATCH')
                <button type="submit" class="btn btn-outline-danger">Batalkan Transaksi</button>
            </form>
        @endif
        <a href="{{ route('sales.index') }}" class="btn btn-secondary">Kembali</a>
    </div>
</div>

<div class="card border-0 shadow-sm mb-4">
    <div class="card-body p-4">
        <!-- Header Khusus Mode Cetak Struk -->
        <div class="print-only text-center pb-3 mb-4 border-bottom">
            <h3 class="fw-bold mb-1">RUNNERSVAULT</h3>
            <p class="small text-muted mb-0">Official Store Perlengkapan & Apparel Lari Indonesia</p>
            <small class="text-muted">Jl. Jenderal Sudirman No. 88, Jakarta • Telp: (021) 555-RUNNER</small>
            <div class="fw-bold mt-2">STRUK RESMI PEMBAYARAN KASIR</div>
        </div>

        <div class="row g-3">
            <div class="col-md-3 col-6">
                <small class="text-muted d-block">Nomor Invoice</small>
                <strong class="font-monospace fs-6">{{ $sale->sale_number }}</strong>
            </div>
            <div class="col-md-3 col-6">
                <small class="text-muted d-block">Tanggal Transaksi</small>
                <strong>{{ $sale->sold_at ? $sale->sold_at->format('d M Y, H:i') : '-' }} WIB</strong>
            </div>
            <div class="col-md-2 col-6">
                <small class="text-muted d-block">Kasir / Petugas</small>
                <strong>{{ $sale->user->name ?? '-' }}</strong>
            </div>
            <div class="col-md-2 col-6">
                <small class="text-muted d-block">Metode Pembayaran</small>
                @php
                    $pm = strtolower($sale->payment_method ?? 'cash');
                    $pmConfig = match($pm) {
                        'qris' => ['class' => 'bg-info-subtle text-info-emphasis border-info-subtle', 'icon' => 'bi-qr-code-scan', 'label' => 'QRIS'],
                        'transfer' => ['class' => 'bg-primary-subtle text-primary border-primary-subtle', 'icon' => 'bi-bank', 'label' => 'TRANSFER'],
                        'card' => ['class' => 'bg-warning-subtle text-warning-emphasis border-warning-subtle', 'icon' => 'bi-credit-card', 'label' => 'CARD'],
                        default => ['class' => 'bg-success-subtle text-success border-success-subtle', 'icon' => 'bi-cash-coin', 'label' => 'CASH'],
                    };
                @endphp
                <span class="badge {{ $pmConfig['class'] }} border px-2.5 py-1.5 fw-bold d-inline-flex align-items-center gap-1">
                    <i class="bi {{ $pmConfig['icon'] }}"></i> {{ $pmConfig['label'] }}
                </span>
            </div>
            <div class="col-md-2 col-6">
                <small class="text-muted d-block">Status Transaksi</small>
                @if($sale->status === 'completed')
                    <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-1.5">
                        <i class="bi bi-check-circle me-1"></i> SELESAI
                    </span>
                @else
                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-3 py-1.5">
                        <i class="bi bi-x-circle me-1"></i> DIBATALKAN
                    </span>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-header bg-white py-3">
        <h6 class="mb-0 fw-bold">Daftar Item yang Terjual</h6>
    </div>
    <div class="card-body p-0">
        <table class="table align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th class="ps-4">Item Terjual</th>
                    <th>Kategori</th>
                    <th class="text-end">Harga Satuan</th>
                    <th class="text-center">Kuantitas</th>
                    <th class="text-end pe-4">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @foreach($sale->items as $item)
                <tr>
                    <td class="ps-4">
                        <div class="d-flex align-items-center gap-3">
                            <img src="{{ $item->product->image_url }}" alt="" class="rounded-3 border shadow-sm flex-shrink-0" style="width: 44px; height: 44px; object-fit: cover;">
                            <div>
                                <div class="fw-semibold text-dark">{{ $item->product->name ?? '-' }}</div>
                                <code class="small text-muted">{{ $item->product->sku ?? '-' }}</code>
                            </div>
                        </div>
                    </td>
                    <td><span class="badge bg-light text-dark border">{{ $item->product->category->name ?? '-' }}</span></td>
                    <td class="text-end">Rp {{ number_format($item->unit_price, 0, ',', '.') }}</td>
                    <td class="text-center fw-bold">{{ $item->quantity }} unit</td>
                    <td class="text-end pe-4 fw-semibold text-primary">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot class="table-light">
                <tr>
                    <th colspan="4" class="text-end py-3">Total Penjualan:</th>
                    <th class="text-end text-primary fs-5 pe-4 py-3">Rp {{ number_format($sale->total, 0, ',', '.') }}</th>
                </tr>
            </tfoot>
        </table>
    </div>

    <!-- Catatan Penutup Struk Saat Dicetak -->
    <div class="print-only text-center p-4 border-top">
        <p class="small fw-semibold mb-1">Terima kasih telah berbelanja di RunnersVault!</p>
        <small class="text-muted d-block">Barang yang sudah dibeli tidak dapat ditukar kecuali ada cacat produksi.</small>
        <small class="text-muted">Simpan struk ini sebagai bukti pembayaran yang sah.</small>
    </div>
</div>
@endsection
