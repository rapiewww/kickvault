@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="fw-bold mb-1"><i class="bi bi-box-seam me-2"></i>Riwayat Pesanan Saya</h3>
        <p class="text-muted mb-0">Daftar transaksi pembelian perlengkapan lari Anda di RunnersVault</p>
    </div>
    <div class="d-flex align-items-center gap-2">
        <a href="{{ route('customer.catalog') }}" class="btn btn-dark">
            <i class="bi bi-shop me-1"></i> Belanja Lagi
        </a>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">Nomor Order</th>
                        <th>Tanggal Pemesanan</th>
                        <th>Daftar Produk</th>
                        <th class="text-end">Total Belanja</th>
                        <th class="text-center">Status</th>
                        <th class="text-end pe-4">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($orders as $order)
                    <tr>
                        <td class="ps-4">
                            <span class="fw-bold font-monospace text-dark">{{ $order->sale_number }}</span>
                        </td>
                        <td>
                            <div>{{ $order->sold_at ? $order->sold_at->format('d M Y') : '-' }}</div>
                            <small class="text-muted">{{ $order->sold_at ? $order->sold_at->format('H:i') : '' }} WIB</small>
                        </td>
                        <td>
                            <ul class="list-unstyled mb-0 small">
                                @foreach($order->items as $item)
                                    <li class="d-flex align-items-center gap-2 mb-1">
                                        <img src="{{ $item->product->image_url ?? '' }}" class="rounded-2 border flex-shrink-0" style="width: 26px; height: 26px; object-fit: cover;">
                                        <span class="text-secondary">{{ $item->product->name ?? 'Produk' }}</span>
                                        <span class="badge bg-light text-dark border">x{{ $item->quantity }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        </td>
                        <td class="text-end fw-bold text-primary">
                            Rp {{ number_format($order->total, 0, ',', '.') }}
                        </td>
                        <td class="text-center">
                            @if($order->status === 'completed')
                                <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-2">
                                    <i class="bi bi-check-circle me-1"></i> Selesai
                                </span>
                            @else
                                <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-3 py-2">
                                    <i class="bi bi-x-circle me-1"></i> Dibatalkan
                                </span>
                            @endif
                        </td>
                        <td class="text-end pe-4">
                            <a href="{{ route('customer.orders.show', $order->id) }}" class="btn btn-sm btn-outline-dark">
                                <i class="bi bi-receipt me-1"></i> Detail Nota
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-5 text-muted">
                            <i class="bi bi-cart-x fs-1 d-block mb-2 text-muted"></i>
                            <p class="fw-semibold mb-1">Anda belum pernah melakukan pemesanan</p>
                            <small class="text-muted d-block mb-3">Yuk jelajahi koleksi perlengkapan lari kami sekarang!</small>
                            <a href="{{ route('customer.catalog') }}" class="btn btn-dark btn-sm">Mulai Belanja</a>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="mt-4">
    {{ $orders->links() }}
</div>
@endsection
