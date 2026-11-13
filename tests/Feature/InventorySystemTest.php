<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Category;
use App\Models\Supplier;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InventorySystemTest extends TestCase
{
    use RefreshDatabase;

    private function createSetupData(): array
    {
        $admin = User::create([
            'name' => 'Admin Test',
            'email' => 'admin@test.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
        ]);

        $staff = User::create([
            'name' => 'Staff Test',
            'email' => 'staff@test.com',
            'password' => bcrypt('password'),
            'role' => 'staff',
        ]);

        $customer = User::create([
            'name' => 'Customer Test',
            'email' => 'customer@test.com',
            'password' => bcrypt('password'),
            'role' => 'customer',
        ]);

        $category = Category::create(['name' => 'Running Shoes']);

        $supplier = Supplier::create([
            'name' => 'PT Ortus Indonesia',
            'phone' => '081234567890',
            'email' => 'supplier@test.com',
        ]);

        $product = Product::create([
            'category_id' => $category->id,
            'sku' => 'SHO-ORT-001',
            'name' => 'Ortuseight Hyperblast',
            'purchase_price' => 400000,
            'selling_price' => 550000,
            'stock' => 10,
            'minimum_stock' => 3,
        ]);

        return compact('admin', 'staff', 'customer', 'category', 'supplier', 'product');
    }

    // 1. Test Auth: Guest tidak dapat membuka dashboard; password salah ditolak
    public function test_guest_cannot_access_dashboard_and_wrong_password_rejected(): void
    {
        $this->get('/dashboard')->assertRedirect('/login');

        $data = $this->createSetupData();

        $this->post('/login', [
            'email' => $data['admin']->email,
            'password' => 'wrong-password',
        ])->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    // 2. Test Product: SKU duplikat ditolak; harga negatif ditolak
    public function test_duplicate_sku_and_negative_price_are_rejected(): void
    {
        $data = $this->createSetupData();

        $this->actingAs($data['admin'])->post('/products', [
            'sku' => 'SHO-ORT-001', // Duplikat
            'name' => 'Sepatu Baru',
            'category_id' => $data['category']->id,
            'purchase_price' => -5000, // Negatif
            'selling_price' => -1000,
            'stock' => 5,
            'minimum_stock' => 1,
        ])->assertSessionHasErrors(['sku', 'purchase_price', 'selling_price']);
    }

    // 3. Test Purchase: Pembelian sukses menambah stok sesuai kuantitas
    public function test_purchase_transaction_increases_product_stock(): void
    {
        $data = $this->createSetupData();

        $this->actingAs($data['staff'])->post('/purchases', [
            'supplier_id' => $data['supplier']->id,
            'purchased_at' => now()->toDateString(),
            'products' => [
                [
                    'id' => $data['product']->id,
                    'quantity' => 5,
                    'unit_cost' => 400000,
                ],
            ],
        ])->assertRedirect('/purchases');

        $this->assertEquals(15, $data['product']->fresh()->stock);
    }

    // 4. Test Sale: Penjualan sukses mengurangi stok sesuai kuantitas
    public function test_sale_transaction_decreases_product_stock(): void
    {
        $data = $this->createSetupData();

        $this->actingAs($data['staff'])->post('/sales', [
            'sold_at' => now()->toDateString(),
            'products' => [
                [
                    'id' => $data['product']->id,
                    'quantity' => 4,
                ],
            ],
        ])->assertRedirect('/sales');

        $this->assertEquals(6, $data['product']->fresh()->stock);
    }

    // 5. Test Insufficient Stock: Penjualan dengan stok tidak cukup ditolak tanpa perubahan data
    public function test_sale_with_insufficient_stock_is_rejected_without_changing_data(): void
    {
        $data = $this->createSetupData();

        $this->actingAs($data['staff'])->post('/sales', [
            'sold_at' => now()->toDateString(),
            'products' => [
                [
                    'id' => $data['product']->id,
                    'quantity' => 25, // Stok hanya 10
                ],
            ],
        ])->assertSessionHasErrors('products');

        $this->assertEquals(10, $data['product']->fresh()->stock);
        $this->assertDatabaseCount('sales', 0);
    }

    // 6. Test Atomicity: Jika satu item gagal, seluruh transaksi rollback
    public function test_transaction_rolls_back_all_items_if_one_item_fails(): void
    {
        $data = $this->createSetupData();

        $product2 = Product::create([
            'category_id' => $data['category']->id,
            'sku' => 'SHO-NIK-002',
            'name' => 'Nike Alphafly',
            'purchase_price' => 3000000,
            'selling_price' => 4000000,
            'stock' => 2,
            'minimum_stock' => 1,
        ]);

        $this->actingAs($data['staff'])->post('/sales', [
            'sold_at' => now()->toDateString(),
            'products' => [
                ['id' => $data['product']->id, 'quantity' => 3], // Valid (stok 10)
                ['id' => $product2->id, 'quantity' => 10],       // Gagal (stok hanya 2)
            ],
        ])->assertSessionHasErrors('products');

        // Pastikan stok produk pertama TIDAK berkurang karena rollback atomik
        $this->assertEquals(10, $data['product']->fresh()->stock);
        $this->assertEquals(2, $product2->fresh()->stock);
        $this->assertDatabaseCount('sales', 0);
    }

    // 7. Test Authorization: Staff tidak dapat mengakses fungsi Admin
    public function test_staff_cannot_access_admin_routes(): void
    {
        $data = $this->createSetupData();

        $this->actingAs($data['staff'])->get('/categories')->assertStatus(403);
        $this->actingAs($data['staff'])->get('/suppliers')->assertStatus(403);
        $this->actingAs($data['staff'])->get('/products/create')->assertStatus(403);
        $this->actingAs($data['staff'])->delete("/products/{$data['product']->id}")->assertStatus(403);
    }

    // 8. Test Search & Filter: Filter produk menghasilkan data yang sesuai
    public function test_product_search_and_category_filter_work_correctly(): void
    {
        $data = $this->createSetupData();

        $response = $this->actingAs($data['staff'])->get('/products?search=Hyperblast');
        $response->assertStatus(200)->assertSee('Ortuseight Hyperblast');
    }

    // 9. Test Customer Order: Customer dapat memesan produk dan stok berkurang secara otomatis
    public function test_customer_can_order_products_and_stock_decreases_automatically(): void
    {
        $data = $this->createSetupData();

        $response = $this->actingAs($data['customer'])->post('/customer/checkout', [
            'products' => [
                [
                    'id' => $data['product']->id,
                    'quantity' => 3,
                ],
            ],
        ]);

        $response->assertRedirect('/customer/orders');
        $this->assertEquals(7, $data['product']->fresh()->stock);
        $this->assertDatabaseHas('sales', [
            'user_id' => $data['customer']->id,
            'status' => 'completed',
            'total' => 3 * 550000,
        ]);
    }

    // 10. Test Customer Authorization: Customer ditolak (403) jika mengakses rute internal / admin
    public function test_customer_cannot_access_dashboard_or_internal_routes(): void
    {
        $data = $this->createSetupData();

        $this->actingAs($data['customer'])->get('/dashboard')->assertStatus(403);
        $this->actingAs($data['customer'])->get('/products/create')->assertStatus(403);
        $this->actingAs($data['customer'])->get('/categories')->assertStatus(403);
        $this->actingAs($data['customer'])->get('/purchases')->assertStatus(403);
        $this->actingAs($data['customer'])->get('/sales')->assertStatus(403);
    }

    // 11. Test Customer Register: Pengunjung dapat mendaftar akun customer baru dan diarahkan ke katalog
    public function test_guest_can_register_as_customer_and_redirect_to_catalog(): void
    {
        $response = $this->post('/register', [
            'name' => 'Pelanggan Baru',
            'email' => 'newcustomer@test.com',
            'password' => 'secret123',
            'password_confirmation' => 'secret123',
        ]);

        $response->assertRedirect('/customer/catalog');
        $this->assertAuthenticated();
        $this->assertDatabaseHas('users', [
            'email' => 'newcustomer@test.com',
            'role' => 'customer',
        ]);
    }

    // 12. Test Customer Isolation: Customer tidak dapat melihat detail pesanan milik pelanggan lain
    public function test_customer_cannot_view_other_customers_order(): void
    {
        $data = $this->createSetupData();

        $otherCustomer = User::create([
            'name' => 'Other Customer',
            'email' => 'other@test.com',
            'password' => bcrypt('password'),
            'role' => 'customer',
        ]);

        $sale = \App\Models\Sale::create([
            'user_id' => $otherCustomer->id,
            'sale_number' => 'ORD-OTHER123',
            'total' => 550000,
            'status' => 'completed',
            'sold_at' => now(),
        ]);

        $this->actingAs($data['customer'])->get("/customer/orders/{$sale->id}")->assertStatus(403);
    }

    // 13. Test Product Image: Upload gambar produk & accessor image_url berfungsi
    public function test_product_image_upload_and_accessor_work_correctly(): void
    {
        \Illuminate\Support\Facades\Storage::fake('public');
        $data = $this->createSetupData();

        $file = \Illuminate\Http\UploadedFile::fake()->image('shoe.jpg');

        $response = $this->actingAs($data['admin'])->post('/products', [
            'sku' => 'SHO-IMG-001',
            'name' => 'Sepatu Bergambar',
            'category_id' => $data['category']->id,
            'purchase_price' => 500000,
            'selling_price' => 750000,
            'stock' => 10,
            'minimum_stock' => 2,
            'image' => $file,
        ]);

        $response->assertRedirect('/products');
        $product = Product::where('sku', 'SHO-IMG-001')->first();
        $this->assertNotNull($product->image);
        \Illuminate\Support\Facades\Storage::disk('public')->assertExists($product->image);
        $this->assertStringContainsString('storage/products/', $product->image_url);
    }

    // 14. Test Role-Based Redirect & Logout: Redirect login sesuai role & logout via POST
    public function test_role_based_login_redirect_and_logout(): void
    {
        $data = $this->createSetupData();

        // Admin login -> redirect ke dashboard
        $responseAdmin = $this->post('/login', [
            'email' => $data['admin']->email,
            'password' => 'password',
        ]);
        $responseAdmin->assertRedirect('/dashboard');

        // Logout via POST (dilindungi CSRF)
        $this->post('/logout')->assertRedirect('/login');
        $this->assertGuest();

        // Customer login -> redirect ke customer catalog
        $responseCustomer = $this->post('/login', [
            'email' => $data['customer']->email,
            'password' => 'password',
        ]);
        $responseCustomer->assertRedirect('/customer/catalog');
    }

    // 15. Test Product Update: Perubahan stok manual diabaikan (stok tetap konsisten)
    public function test_updating_product_ignores_manual_stock_changes(): void
    {
        $data = $this->createSetupData();

        $response = $this->actingAs($data['admin'])->put("/products/{$data['product']->id}", [
            'sku' => $data['product']->sku,
            'name' => 'Ortuseight Hyperblast Edited',
            'category_id' => $data['category']->id,
            'purchase_price' => 420000,
            'selling_price' => 580000,
            'stock' => 999, // Percobaan ubah stok manual
            'minimum_stock' => 5,
        ]);

        $response->assertRedirect('/products');
        $this->assertEquals(10, $data['product']->fresh()->stock); // Stok tetap 10
        $this->assertEquals('Ortuseight Hyperblast Edited', $data['product']->fresh()->name);
    }

    // 16. Test Payment Method: Transaksi penjualan berhasil menyimpan metode pembayaran terpilih
    public function test_sale_saves_selected_payment_method(): void
    {
        $data = $this->createSetupData();

        $response = $this->actingAs($data['staff'])->post('/sales', [
            'sold_at' => now()->toDateString(),
            'payment_method' => 'qris',
            'products' => [
                [
                    'id' => $data['product']->id,
                    'quantity' => 2,
                ],
            ],
        ]);

        $response->assertRedirect('/sales');
        $this->assertDatabaseHas('sales', [
            'user_id' => $data['staff']->id,
            'payment_method' => 'qris',
            'status' => 'completed',
        ]);
        $this->assertEquals(8, $data['product']->fresh()->stock);
    }
}

