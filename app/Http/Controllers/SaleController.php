<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class SaleController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $status = $request->input('status');

        $sales = Sale::with(['user', 'items.product'])
            ->when($search, function ($query, $search) {
                $query->where('sale_number', 'like', "%{$search}%");
            })
            ->when($startDate, function ($query, $startDate) {
                $query->whereDate('sold_at', '>=', $startDate);
            })
            ->when($endDate, function ($query, $endDate) {
                $query->whereDate('sold_at', '<=', $endDate);
            })
            ->when($status, function ($query, $status) {
                $query->where('status', $status);
            })
            ->latest('sold_at')
            ->paginate(5)
            ->withQueryString();

        return view('sales.index', compact('sales', 'search', 'startDate', 'endDate', 'status'));
    }

    public function create(Request $request)
    {
        $search = $request->input('search');
        $categoryId = $request->input('category_id');

        $categories = \App\Models\Category::withCount(['products' => function ($q) {
            $q->where('stock', '>', 0);
        }])->orderBy('name')->get();

        $products = Product::with('category')
            ->where('stock', '>', 0)
            ->when($search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('sku', 'like', "%{$search}%");
                });
            })
            ->when($categoryId, function ($query, $categoryId) {
                $query->where('category_id', $categoryId);
            })
            ->latest()
            ->get();

        $totalAllProducts = Product::where('stock', '>', 0)->count();

        return view('sales.create', compact('products', 'categories', 'search', 'categoryId', 'totalAllProducts'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'sold_at' => ['required', 'date'],
            'payment_method' => ['nullable', 'string', 'in:cash,qris,transfer,card'],
            'products' => ['required', 'array', 'min:1'],
            'products.*.id' => ['required', 'exists:products,id'],
            'products.*.quantity' => ['required', 'integer', 'min:1'],
        ]);

        try {
            // Menggunakan closure DB::transaction sesuai contoh Slide 16 dosen
            // Otomatis commit jika sukses, dan otomatis rollBack jika ada Exception
            $sale = DB::transaction(function () use ($request) {
                $sale = Sale::create([
                    'user_id' => auth()->id(),
                    'sale_number' => 'INV-' . strtoupper(uniqid()),
                    'total' => 0,
                    'status' => 'completed',
                    'payment_method' => $request->input('payment_method', 'cash') ?: 'cash',
                    'sold_at' => $request->sold_at,
                ]);

                $totalSale = 0;

                foreach ($request->products as $item) {
                    // Kunci baris produk di DB untuk mencegah race condition (Slide 16)
                    $product = Product::lockForUpdate()->findOrFail($item['id']);
                    $qty = (int) $item['quantity'];

                    // Cek stok langsung terhadap stok terkini di DB
                    if ($qty < 1 || $product->stock < $qty) {
                        throw ValidationException::withMessages([
                            'products' => "Stok produk '{$product->name}' tidak mencukupi! Sisa stok tersedia: {$product->stock} unit.",
                        ]);
                    }

                    // Harga diambil dari database server, bukan dari input browser (Slide 8 & 16)
                    $subtotal = $qty * $product->selling_price;

                    SaleItem::create([
                        'sale_id' => $sale->id,
                        'product_id' => $product->id,
                        'quantity' => $qty,
                        'unit_price' => $product->selling_price,
                        'subtotal' => $subtotal,
                    ]);

                    // Langsung kurangi stok di dalam loop yang sama agar jika ada produk
                    // yang dipilih 2x di baris berbeda, pengecekan baris berikutnya tetap akurat
                    $product->decrement('stock', $qty);

                    $totalSale += $subtotal;
                }

                $sale->update(['total' => $totalSale]);

                return $sale;
            });

            if ($request->boolean('redirect_to_receipt')) {
                return redirect()->route('sales.show', $sale->id)->with('success', 'Pembayaran berhasil! Silakan cetak struk.');
            }

            return redirect()->route('sales.index')->with('success', 'Transaksi penjualan berhasil! Stok produk telah dikurangi secara otomatis.');

        } catch (ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            report($e);
            return back()->with('error', 'Terjadi kesalahan sistem saat memproses transaksi.')->withInput();
        }
    }

    public function show(Sale $sale)
    {
        $sale->load(['user', 'items.product.category']);
        return view('sales.show', compact('sale'));
    }

    public function cancel(Sale $sale)
    {
        if ($sale->status === 'cancelled') {
            return back()->with('error', 'Transaksi penjualan ini sudah dibatalkan sebelumnya.');
        }

        try {
            DB::transaction(function () use ($sale) {
                $sale->load('items');

                // Kembalikan stok produk sesuai jumlah yang dibatalkan (Slide 8)
                foreach ($sale->items as $item) {
                    $product = Product::lockForUpdate()->findOrFail($item->product_id);
                    $product->increment('stock', $item->quantity);
                }

                $sale->update(['status' => 'cancelled']);
            });

            return back()->with('success', "Transaksi {$sale->sale_number} berhasil dibatalkan dan stok produk telah dikembalikan.");
        } catch (\Exception $e) {
            report($e);
            return back()->with('error', 'Terjadi kesalahan saat membatalkan transaksi.');
        }
    }
}
