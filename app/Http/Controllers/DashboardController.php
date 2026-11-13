<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Sale;
use App\Models\Purchase;
use App\Models\User;
use Illuminate\Support\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        // 1. KPI Utama (5 Kartu Pastel)
        $monthSalesTotal = Sale::whereMonth('sold_at', Carbon::now()->month)
            ->whereYear('sold_at', Carbon::now()->year)
            ->where('status', 'completed')
            ->sum('total');

        $todaySalesTotal = Sale::whereDate('sold_at', Carbon::today())
            ->where('status', 'completed')
            ->sum('total');

        $totalProducts = Product::count();
        $totalStockUnits = (int) Product::sum('stock');

        $averageOrderValue = (float) (Sale::where('status', 'completed')->avg('total') ?? 0);
        $totalTransactions = Sale::count() + Purchase::count();

        // Produk dengan stok <= minimum_stock (Slide 13 & 18)
        $lowStockProducts = Product::with('category')
            ->whereColumn('stock', '<=', 'minimum_stock')
            ->get();

        // 2. Data Grafik Summary (7 Hari Terakhir)
        $chartLabels = [];
        $chartSalesData = [];
        $chartPurchasesData = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $chartLabels[] = $date->format('d M');

            $chartSalesData[] = (float) Sale::whereDate('sold_at', $date)
                ->where('status', 'completed')
                ->sum('total');

            $chartPurchasesData[] = (float) Purchase::whereDate('purchased_at', $date)
                ->where('status', 'completed')
                ->sum('total');
        }

        // 3. Most Selling Products (4 Produk Terlaris)
        $mostSellingProducts = Product::withSum(['saleItems as total_sold' => function ($q) {
            $q->whereHas('sale', fn($s) => $s->where('status', 'completed'));
        }], 'quantity')->orderByDesc('total_sold')->take(4)->get();

        // 4. Recent Orders (5 Transaksi Penjualan Terbaru)
        $recentSales = Sale::with(['user', 'items.product'])->latest('sold_at')->take(5)->get();

        // 5. Top Customers / Users (3 Pengguna Teraktif)
        $topUsers = User::withCount(['sales' => fn($q) => $q->where('status', 'completed')])
            ->orderByDesc('sales_count')
            ->take(3)
            ->get();

        return view('dashboard', compact(
            'monthSalesTotal',
            'todaySalesTotal',
            'totalProducts',
            'totalStockUnits',
            'averageOrderValue',
            'totalTransactions',
            'lowStockProducts',
            'chartLabels',
            'chartSalesData',
            'chartPurchasesData',
            'mostSellingProducts',
            'recentSales',
            'topUsers'
        ));
    }
}
