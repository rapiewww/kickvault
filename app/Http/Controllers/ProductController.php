<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $categoryId = $request->input('category_id');
        $categories = Category::orderBy('name')->get();

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
            ->paginate(10)
            ->withQueryString();

        return view('products.index', compact('products', 'categories', 'search', 'categoryId'));
    }

    public function create()
    {
        $categories = Category::all();
        return view('products.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'sku' => ['required', 'string', 'max:50', 'unique:products,sku'],
            'name' => ['required', 'string', 'max:150'],
            'category_id' => ['required', 'exists:categories,id'],
            'purchase_price' => ['required', 'numeric', 'min:0'],
            'selling_price' => ['required', 'numeric', 'min:0'],
            'stock' => ['required', 'integer', 'min:0'],
            'minimum_stock' => ['required', 'integer', 'min:0'],
            'image' => ['nullable', 'image', 'max:2048'],
            'image_url_input' => ['nullable', 'url', 'max:500'],
        ]);

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('products', 'public');
        } elseif ($request->filled('image_url_input')) {
            $data['image'] = $request->input('image_url_input');
        }
        unset($data['image_url_input']);

        Product::create($data);

        return redirect()->route('products.index')->with('success', 'Produk perlengkapan lari berhasil ditambahkan!');
    }

    public function show(Product $product)
    {
        $product->load(['category', 'purchaseItems.purchase.supplier', 'saleItems.sale.user']);
        return view('products.show', compact('product'));
    }

    public function edit(Product $product)
    {
        $categories = Category::all();
        return view('products.edit', compact('product', 'categories'));
    }

    public function update(Request $request, Product $product)
    {
        $data = $request->validate([
            'sku' => ['required', 'string', 'max:50', 'unique:products,sku,' . $product->id],
            'name' => ['required', 'string', 'max:150'],
            'category_id' => ['required', 'exists:categories,id'],
            'purchase_price' => ['required', 'numeric', 'min:0'],
            'selling_price' => ['required', 'numeric', 'min:0'],
            'minimum_stock' => ['required', 'integer', 'min:0'],
            'image' => ['nullable', 'image', 'max:2048'],
            'image_url_input' => ['nullable', 'url', 'max:500'],
        ]);

        unset($data['stock']);

        if ($request->hasFile('image')) {
            if ($product->image && !str_starts_with($product->image, 'http')) {
                Storage::disk('public')->delete($product->image);
            }
            $data['image'] = $request->file('image')->store('products', 'public');
        } elseif ($request->filled('image_url_input')) {
            $data['image'] = $request->input('image_url_input');
        }
        unset($data['image_url_input']);

        $product->update($data);

        return redirect()->route('products.index')->with('success', 'Data produk berhasil diperbarui!');
    }

    public function destroy(Product $product)
    {
        // Sesuai aturan Slide 8: Produk yang sudah dipakai transaksi dilarang hard delete / gunakan proteksi
        if ($product->saleItems()->exists() || $product->purchaseItems()->exists()) {
            return back()->with('error', 'Produk tidak dapat dihapus karena sudah memiliki riwayat transaksi!');
        }

        $product->delete();

        return redirect()->route('products.index')->with('success', 'Produk berhasil dihapus.');
    }
}
