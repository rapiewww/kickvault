<?php

namespace App\Http\Controllers;

use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\Supplier;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PurchaseController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $status = $request->input('status');

        $purchases = Purchase::with(['supplier', 'user', 'items.product'])
            ->when($search, function ($query, $search) {
                $query->where('purchase_number', 'like', "%{$search}%");
            })
            ->when($startDate, function ($query, $startDate) {
                $query->whereDate('purchased_at', '>=', $startDate);
            })
            ->when($endDate, function ($query, $endDate) {
                $query->whereDate('purchased_at', '<=', $endDate);
            })
            ->when($status, function ($query, $status) {
                $query->where('status', $status);
            })
            ->latest('purchased_at')
            ->paginate(5)
            ->withQueryString();

        return view('purchases.index', compact('purchases', 'search', 'startDate', 'endDate', 'status'));
    }

    public function create()
    {
        $suppliers = Supplier::all();
        $products = Product::all();
        return view('purchases.create', compact('suppliers', 'products'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'supplier_id' => ['required', 'exists:suppliers,id'],
            'purchased_at' => ['required', 'date'],
            'products' => ['required', 'array', 'min:1'],
            'products.*.id' => ['required', 'exists:products,id'],
            'products.*.quantity' => ['required', 'integer', 'min:1'],
            'products.*.unit_cost' => ['required', 'numeric', 'min:0'],
        ]);

        try {
            DB::transaction(function () use ($request) {
                $purchase = Purchase::create([
                    'supplier_id' => $request->supplier_id,
                    'user_id' => auth()->id(),
                    'purchase_number' => 'PO-' . strtoupper(uniqid()),
                    'total' => 0,
                    'status' => 'completed',
                    'purchased_at' => $request->purchased_at,
                ]);

                $totalPurchase = 0;

                foreach ($request->products as $item) {
                    $product = Product::lockForUpdate()->findOrFail($item['id']);
                    $qty = (int) $item['quantity'];
                    $unitCost = (float) $item['unit_cost'];
                    $subtotal = $qty * $unitCost;

                    PurchaseItem::create([
                        'purchase_id' => $purchase->id,
                        'product_id' => $product->id,
                        'quantity' => $qty,
                        'unit_cost' => $unitCost,
                        'subtotal' => $subtotal,
                    ]);

                    // Tambah stok secara atomik
                    $product->increment('stock', $qty);
                    $totalPurchase += $subtotal;
                }

                $purchase->update(['total' => $totalPurchase]);
            });

            return redirect()->route('purchases.index')->with('success', 'Transaksi pembelian berhasil! Stok produk telah bertambah secara otomatis.');

        } catch (\Exception $e) {
            report($e);
            return back()->with('error', 'Terjadi kesalahan saat menyimpan transaksi pembelian.')->withInput();
        }
    }

    public function show(Purchase $purchase)
    {
        $purchase->load(['supplier', 'user', 'items.product.category']);
        return view('purchases.show', compact('purchase'));
    }

    public function cancel(Purchase $purchase)
    {
        if ($purchase->status === 'cancelled') {
            return back()->with('error', 'Transaksi pembelian ini sudah dibatalkan sebelumnya.');
        }

        try {
            DB::transaction(function () use ($purchase) {
                $purchase->load('items');

                foreach ($purchase->items as $item) {
                    $product = Product::lockForUpdate()->findOrFail($item->product_id);

                    // Pastikan jika pembelian dibatalkan, stok saat ini cukup untuk ditarik kembali
                    if ($product->stock < $item->quantity) {
                        throw ValidationException::withMessages([
                            'cancel' => "Pembatalan gagal! Stok produk '{$product->name}' saat ini ({$product->stock}) lebih sedikit dari jumlah pembelian yang akan dibatalkan ({$item->quantity}).",
                        ]);
                    }

                    $product->decrement('stock', $item->quantity);
                }

                $purchase->update(['status' => 'cancelled']);
            });

            return back()->with('success', "Transaksi {$purchase->purchase_number} berhasil dibatalkan dan stok telah disesuaikan.");
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors());
        } catch (\Exception $e) {
            report($e);
            return back()->with('error', 'Terjadi kesalahan saat membatalkan transaksi.');
        }
    }
}
