<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CustomerController extends Controller
{
    public function catalog(Request $request)
    {
        $search = $request->input('search');
        $categoryId = $request->input('category_id');

        $categories = Category::withCount('products')->orderBy('name')->get();
        $totalAllProducts = Product::count();

        $products = Product::with('category')
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
            ->paginate(12)
            ->withQueryString();

        return view('customer.catalog', compact('products', 'categories', 'search', 'categoryId', 'totalAllProducts'));
    }

    public function checkout(Request $request)
    {
        $request->validate([
            'payment_method' => ['nullable', 'string', 'in:cash,qris,transfer,card'],
            'products' => ['required', 'array', 'min:1'],
            'products.*.id' => ['required', 'exists:products,id'],
            'products.*.quantity' => ['required', 'integer', 'min:1'],
        ]);

        try {
            $sale = DB::transaction(function () use ($request) {
                $sale = Sale::create([
                    'user_id' => auth()->id(),
                    'sale_number' => 'ORD-' . strtoupper(uniqid()),
                    'total' => 0,
                    'status' => 'completed',
                    'payment_method' => $request->input('payment_method', 'cash') ?: 'cash',
                    'sold_at' => now(),
                ]);

                $totalSale = 0;

                foreach ($request->products as $item) {
                    $product = Product::lockForUpdate()->findOrFail($item['id']);
                    $qty = (int) $item['quantity'];

                    if ($qty < 1 || $product->stock < $qty) {
                        throw ValidationException::withMessages([
                            'products' => "Stok produk '{$product->name}' tidak mencukupi! Sisa stok tersedia: {$product->stock} unit.",
                        ]);
                    }

                    $subtotal = $qty * $product->selling_price;

                    SaleItem::create([
                        'sale_id' => $sale->id,
                        'product_id' => $product->id,
                        'quantity' => $qty,
                        'unit_price' => $product->selling_price,
                        'subtotal' => $subtotal,
                    ]);

                    $product->decrement('stock', $qty);

                    $totalSale += $subtotal;
                }

                $sale->update(['total' => $totalSale]);

                return $sale;
            });

            if ($request->boolean('redirect_to_receipt')) {
                return redirect()->route('customer.orders.show', $sale->id)->with('success', 'Pesanan dan pembayaran berhasil!');
            }

            return redirect()->route('customer.orders')->with('success', 'Pesanan Anda berhasil dibuat dan langsung diproses!');

        } catch (ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            report($e);
            return back()->with('error', 'Terjadi kesalahan sistem saat memproses checkout pesanan Anda.')->withInput();
        }
    }

    public function orders()
    {
        $orders = Sale::with('items.product')
            ->where('user_id', auth()->id())
            ->latest('sold_at')
            ->paginate(5);

        return view('customer.orders', compact('orders'));
    }

    public function orderShow(Sale $sale)
    {
        if ($sale->user_id !== auth()->id()) {
            abort(403, 'Akses ditolak! Anda tidak memiliki izin untuk melihat pesanan ini.');
        }

        $sale->load(['items.product.category']);

        return view('customer.show', compact('sale'));
    }
}
