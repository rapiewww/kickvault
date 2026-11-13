@extends('layouts.app')

@section('page_title', 'Dashboard Overview')

@section('content')
<style>
    .kpi-card {
        border-radius: 18px;
        padding: 1.35rem 1.25rem;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        border: none;
    }
    .kpi-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.04);
    }
    .kpi-title {
        font-size: 0.85rem;
        font-weight: 600;
        color: #4b5563;
        margin-bottom: 0.35rem;
    }
    .kpi-value {
        font-size: 1.45rem;
        font-weight: 800;
        color: #111827;
        letter-spacing: -0.02em;
        line-height: 1.2;
    }
    .kpi-badge {
        background: rgba(255, 255, 255, 0.95);
        border-radius: 50rem;
        padding: 0.28rem 0.65rem;
        font-size: 0.72rem;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        gap: 0.3rem;
        box-shadow: 0 1px 4px rgba(0,0,0,0.04);
    }
    .section-card {
        border-radius: 18px;
        background: #ffffff;
        border: 1px solid #edf2f7;
        box-shadow: 0 2px 10px rgba(0,0,0,0.02);
    }
    .legend-indicator {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        display: inline-block;
    }
</style>

<!-- Greeting Header Row (Subcom Style) -->
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
    <div>
        <h3 class="fw-extrabold text-dark mb-1" style="font-weight: 800; letter-spacing: -0.02em;">
            Welcome Back, {{ auth()->user()->name }}!
        </h3>
        <p class="text-muted small mb-0">Here's what's happening with RunnersVault store today</p>
    </div>
    <div class="d-flex align-items-center gap-2">
        <a href="{{ route('sales.index') }}" class="btn btn-outline-dark rounded-pill px-3 py-2 fw-semibold small">
            <i class="bi bi-clock-history me-1"></i> View All Sales
        </a>
        <a href="{{ route('sales.create') }}" class="btn btn-pos-dark rounded-pill px-3 py-2 fw-semibold small shadow-sm d-flex align-items-center gap-1.5">
            <i class="bi bi-plus-lg"></i>
            <span>Buat Transaksi POS</span>
        </a>
    </div>
</div>

<!-- Baris 1: 5 Kartu KPI Warna Pastel Lembut -->
<div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-5 g-3 mb-4">
    <!-- Card 1: Pastel Peach -->
    <div class="col">
        <div class="kpi-card h-100 d-flex flex-column justify-content-between" style="background-color: #fdf2e9;">
            <div>
                <div class="kpi-title">Penjualan Bulan Ini</div>
                <div class="kpi-value">Rp {{ number_format($monthSalesTotal, 0, ',', '.') }}</div>
            </div>
            <div class="mt-3">
                <span class="kpi-badge text-success">
                    ▲ Active Month
                </span>
            </div>
        </div>
    </div>

    <!-- Card 2: Pastel Mint -->
    <div class="col">
        <div class="kpi-card h-100 d-flex flex-column justify-content-between" style="background-color: #eef7ee;">
            <div>
                <div class="kpi-title">Penjualan Hari Ini</div>
                <div class="kpi-value">Rp {{ number_format($todaySalesTotal, 0, ',', '.') }}</div>
            </div>
            <div class="mt-3">
                <span class="kpi-badge text-success">
                    ▲ Today ({{ now()->format('d M') }})
                </span>
            </div>
        </div>
    </div>

    <!-- Card 3: Pastel Lavender -->
    <div class="col">
        <div class="kpi-card h-100 d-flex flex-column justify-content-between" style="background-color: #eef2ff;">
            <div>
                <div class="kpi-title">Total Produk & Stok</div>
                <div class="kpi-value">{{ $totalProducts }} <span class="fs-6 fw-bold text-secondary">SKU</span></div>
            </div>
            <div class="mt-3">
                <span class="kpi-badge text-primary">
                    ▲ {{ $totalStockUnits }} Unit Tersedia
                </span>
            </div>
        </div>
    </div>

    <!-- Card 4: Pastel Cyan -->
    <div class="col">
        <div class="kpi-card h-100 d-flex flex-column justify-content-between" style="background-color: #eaf6f6;">
            <div>
                <div class="kpi-title">Rata-rata Order (AOV)</div>
                <div class="kpi-value">Rp {{ number_format($averageOrderValue, 0, ',', '.') }}</div>
            </div>
            <div class="mt-3">
                <span class="kpi-badge text-info text-dark">
                    ▲ {{ $totalTransactions }} Transaksi
                </span>
            </div>
        </div>
    </div>

    <!-- Card 5: Pastel Rose -->
    <div class="col">
        <div class="kpi-card h-100 d-flex flex-column justify-content-between" style="background-color: #fbeeee;">
            <div>
                <div class="kpi-title">Stok Rendah (Low Stock)</div>
                <div class="kpi-value">{{ $lowStockProducts->count() }} <span class="fs-6 fw-bold text-secondary">Item</span></div>
            </div>
            <div class="mt-3">
                @if($lowStockProducts->count() > 0)
                    <span class="kpi-badge text-danger">
                        ▼ Perlu Restock
                    </span>
                @else
                    <span class="kpi-badge text-success">
                        ▲ Stok Aman
                    </span>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Baris 2: Grafik Summary & Most Selling Products -->
<div class="row g-4 mb-4">
    <!-- Grafik Summary 7 Hari Terakhir (8 Kolom) -->
    <div class="col-lg-8">
        <div class="section-card p-4 h-100 d-flex flex-column">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-3">
                <h5 class="fw-bold text-dark mb-0">Summary</h5>
                
                <!-- Chart Legend -->
                <div class="d-flex align-items-center gap-3 small">
                    <span class="d-flex align-items-center gap-1.5 fw-semibold text-secondary">
                        <span class="legend-indicator" style="background-color: #10b981;"></span>
                        Sales Revenue
                    </span>
                    <span class="d-flex align-items-center gap-1.5 fw-semibold text-secondary">
                        <span class="legend-indicator" style="background-color: #84cc16;"></span>
                        Purchases Stock-In
                    </span>
                </div>

                <span class="badge bg-light text-secondary border rounded-pill px-3 py-1.5 font-monospace small">
                    Last 7 days
                </span>
            </div>

            <!-- Canvas Chart.js -->
            <div class="flex-grow-1 position-relative" style="min-height: 250px;">
                <canvas id="summaryChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Most Selling Products (4 Kolom) -->
    <div class="col-lg-4">
        <div class="section-card p-4 h-100 d-flex flex-column">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <h5 class="fw-bold text-dark mb-0">Most Selling Products</h5>
                <span class="text-muted"><i class="bi bi-three-dots"></i></span>
            </div>

            <div class="d-flex flex-column gap-3 mt-1 flex-grow-1 justify-content-around">
                @forelse($mostSellingProducts as $product)
                <div class="d-flex align-items-center justify-content-between p-2 rounded-3 bg-light-subtle">
                    <div class="d-flex align-items-center gap-3 overflow-hidden">
                        <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="rounded-3 border shadow-xs flex-shrink-0" style="width: 48px; height: 48px; object-fit: cover;">
                        <div class="overflow-hidden">
                            <h6 class="fw-bold text-dark mb-0 text-truncate" title="{{ $product->name }}" style="max-width: 150px;">
                                {{ $product->name }}
                            </h6>
                            <code class="small text-muted" style="font-size: 0.72rem;">ID: {{ $product->sku }}</code>
                        </div>
                    </div>
                    <span class="badge bg-white text-dark border rounded-pill px-3 py-1.5 font-monospace fw-bold flex-shrink-0" style="font-size: 0.75rem;">
                        {{ $product->total_sold ?? 0 }} Sales
                    </span>
                </div>
                @empty
                <div class="text-center py-4 text-muted small">
                    Belum ada data produk terjual.
                </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

<!-- Baris 3: Recent Orders & (Low Stock Alert + Top Users) -->
<div class="row g-4">
    <!-- Recent Orders (8 Kolom) -->
    <div class="col-lg-8">
        <div class="section-card p-4 h-100">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <h5 class="fw-bold text-dark mb-0">Recent Orders</h5>
                <a href="{{ route('sales.index') }}" class="btn btn-sm btn-outline-dark rounded-pill px-3">
                    View All
                </a>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr class="small text-secondary">
                            <th>Product</th>
                            <th>Customer / Kasir</th>
                            <th>Order ID</th>
                            <th>Date</th>
                            <th class="text-end">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentSales as $sale)
                        @php
                            $firstItem = $sale->items->first();
                        @endphp
                        <tr>
                            <td>
                                <div class="d-flex align-items-center gap-2.5">
                                    <img src="{{ $firstItem?->product?->image_url ?? 'https://images.unsplash.com/photo-1542291026-7eec264c27ff?auto=format&fit=crop&w=600&q=80' }}" 
                                         alt="" class="rounded-3 border shadow-xs flex-shrink-0" style="width: 40px; height: 40px; object-fit: cover;">
                                    <div class="overflow-hidden">
                                        <a href="{{ route('sales.show', $sale->id) }}" class="text-dark fw-bold text-decoration-none text-truncate d-block small" style="max-width: 140px;">
                                            {{ $firstItem?->product?->name ?? 'Order Items' }}
                                        </a>
                                        @if($sale->items->count() > 1)
                                            <span class="badge bg-light text-muted border" style="font-size: 0.65rem;">+{{ $sale->items->count() - 1 }} lainnya</span>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="text-primary fw-semibold small">{{ $sale->user->name ?? 'Guest Runner' }}</span>
                            </td>
                            <td>
                                <code class="small font-monospace">{{ $sale->sale_number }}</code>
                            </td>
                            <td>
                                <span class="text-secondary small">{{ $sale->sold_at ? $sale->sold_at->format('d M Y') : '-' }}</span>
                            </td>
                            <td class="text-end">
                                @if($sale->status === 'completed')
                                    <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2.5 py-1 small fw-semibold">
                                        ● Completed
                                    </span>
                                @else
                                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill px-2.5 py-1 small fw-semibold">
                                        ● Cancelled
                                    </span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-4 text-muted small">
                                Belum ada riwayat transaksi penjualan.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Low Stock Alert & Weekly Top Users (4 Kolom) -->
    <div class="col-lg-4 d-flex flex-column gap-4">
        <!-- Low Stock Alert Card -->
        <div class="section-card p-4">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <div class="d-flex align-items-center gap-2">
                    <i class="bi bi-exclamation-triangle-fill text-danger fs-5"></i>
                    <h5 class="fw-bold text-danger mb-0 fs-6">Low Stock Alert</h5>
                </div>
                <span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill">
                    {{ $lowStockProducts->count() }} Item
                </span>
            </div>

            <div class="d-flex flex-column gap-2.5">
                @forelse($lowStockProducts->take(3) as $product)
                <div class="d-flex align-items-center justify-content-between p-2 rounded-3 border bg-light-subtle">
                    <div class="d-flex align-items-center gap-2.5 overflow-hidden">
                        <img src="{{ $product->image_url }}" alt="" class="rounded-2 border flex-shrink-0" style="width: 36px; height: 36px; object-fit: cover;">
                        <div class="overflow-hidden">
                            <div class="fw-bold text-dark small text-truncate" style="max-width: 120px;">{{ $product->name }}</div>
                            <span class="badge bg-danger-subtle text-danger" style="font-size: 0.65rem;">Sisa: {{ $product->stock }} unit</span>
                        </div>
                    </div>
                    <a href="{{ route('purchases.create') }}" class="btn btn-sm btn-outline-dark rounded-pill py-0.5 px-2.5" style="font-size: 0.75rem;">
                        Restock
                    </a>
                </div>
                @empty
                <div class="p-3 bg-success-subtle text-success rounded-3 small border border-success-subtle text-center">
                    <i class="bi bi-shield-check fs-5 d-block mb-1"></i>
                    Semua stok produk perlengkapan lari aman!
                </div>
                @endforelse
            </div>
        </div>

        <!-- Weekly Top Users Card -->
        <div class="section-card p-4 flex-grow-1">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <h5 class="fw-bold text-dark mb-0 fs-6">Top Users / Customers</h5>
                <span class="text-muted"><i class="bi bi-three-dots"></i></span>
            </div>

            <div class="d-flex flex-column gap-3">
                @forelse($topUsers as $u)
                <div class="d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center gap-2.5">
                        <div class="rounded-circle bg-dark text-white d-flex align-items-center justify-content-center fw-bold shadow-xs" style="width: 36px; height: 36px; font-size: 0.8rem;">
                            {{ strtoupper(substr($u->name, 0, 2)) }}
                        </div>
                        <div>
                            <div class="fw-bold text-dark small">{{ $u->name }}</div>
                            <small class="text-muted" style="font-size: 0.72rem;">{{ $u->sales_count }} Orders</small>
                        </div>
                    </div>
                    <span class="badge bg-light text-secondary border rounded-pill px-2.5 py-1" style="font-size: 0.72rem;">
                        {{ strtoupper($u->role) }}
                    </span>
                </div>
                @empty
                <div class="text-muted small text-center py-2">
                    Belum ada aktivitas user.
                </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

<!-- Chart.js Initialization Script -->
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const canvas = document.getElementById('summaryChart');
        if (!canvas) return;

        const ctx = canvas.getContext('2d');

        // Gradient for Sales line
        const gradientSales = ctx.createLinearGradient(0, 0, 0, 260);
        gradientSales.addColorStop(0, 'rgba(16, 185, 129, 0.28)');
        gradientSales.addColorStop(1, 'rgba(16, 185, 129, 0.0)');

        // Gradient for Purchases line
        const gradientPurchases = ctx.createLinearGradient(0, 0, 0, 260);
        gradientPurchases.addColorStop(0, 'rgba(132, 204, 22, 0.22)');
        gradientPurchases.addColorStop(1, 'rgba(132, 204, 22, 0.0)');

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: {!! json_encode($chartLabels) !!},
                datasets: [
                    {
                        label: 'Sales Revenue',
                        data: {!! json_encode($chartSalesData) !!},
                        borderColor: '#10b981',
                        backgroundColor: gradientSales,
                        borderWidth: 3.5,
                        tension: 0.4,
                        fill: true,
                        pointRadius: 4,
                        pointBackgroundColor: '#ffffff',
                        pointBorderColor: '#10b981',
                        pointBorderWidth: 2,
                        pointHoverRadius: 6,
                    },
                    {
                        label: 'Purchases Stock-In',
                        data: {!! json_encode($chartPurchasesData) !!},
                        borderColor: '#84cc16',
                        backgroundColor: gradientPurchases,
                        borderWidth: 2.5,
                        tension: 0.4,
                        fill: true,
                        pointRadius: 3,
                        pointBackgroundColor: '#ffffff',
                        pointBorderColor: '#84cc16',
                        pointBorderWidth: 2,
                        pointHoverRadius: 5,
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: '#111315',
                        padding: 10,
                        cornerRadius: 8,
                        titleFont: { family: "'Plus Jakarta Sans', sans-serif", weight: 'bold' },
                        bodyFont: { family: "'Plus Jakarta Sans', sans-serif" },
                        callbacks: {
                            label: function(context) {
                                return context.dataset.label + ': Rp ' + Number(context.raw).toLocaleString('id-ID');
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            font: { family: "'Plus Jakarta Sans', sans-serif", size: 12 },
                            color: '#94a3b8'
                        }
                    },
                    y: {
                        grid: {
                            color: '#f1f5f9'
                        },
                        ticks: {
                            font: { family: "'Plus Jakarta Sans', sans-serif", size: 11 },
                            color: '#94a3b8',
                            callback: function(value) {
                                if (value >= 1000000) return (value / 1000000).toFixed(1) + 'M';
                                if (value >= 1000) return (value / 1000).toFixed(0) + 'K';
                                return value;
                            }
                        }
                    }
                }
            }
        });
    });
</script>
@endsection
