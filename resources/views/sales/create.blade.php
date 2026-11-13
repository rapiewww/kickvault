@extends('layouts.app')

@section('page_title', 'Kasir POS (Point of Sale)')

@section('content')
<style>
    .category-pill {
        border-radius: 50rem;
        padding: 0.5rem 1.15rem;
        font-weight: 600;
        font-size: 0.875rem;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        transition: all 0.2s ease;
        white-space: nowrap;
    }
    .category-pill.active {
        background-color: #111827 !important;
        color: #ffffff !important;
        box-shadow: 0 4px 10px rgba(17, 24, 39, 0.15);
    }
    .category-pill.inactive {
        background-color: #ffffff;
        color: #475569;
        border: 1px solid #e2e8f0;
    }
    .category-pill.inactive:hover {
        background-color: #f8fafc;
        color: #0f172a;
        border-color: #cbd5e1;
    }
    .category-badge-count {
        font-size: 0.72rem;
        padding: 0.15rem 0.45rem;
        border-radius: 50rem;
    }
    .product-card {
        border-radius: 16px;
        background: #ffffff;
        border: 1px solid #f1f5f9;
        box-shadow: 0 2px 10px rgba(0,0,0,0.02);
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        overflow: hidden;
    }
    .product-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 25px rgba(0,0,0,0.06);
    }
    .product-img-wrapper {
        position: relative;
        height: 175px;
        width: 100%;
        overflow: hidden;
        border-radius: 12px;
    }
    .product-img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.3s ease;
    }
    .product-card:hover .product-img {
        transform: scale(1.04);
    }
    .status-floating-pill {
        position: absolute;
        top: 10px;
        right: 10px;
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(4px);
        border-radius: 50rem;
        padding: 0.3rem 0.75rem;
        font-size: 0.75rem;
        font-weight: 600;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        display: flex;
        align-items: center;
        gap: 0.4rem;
    }
    .dot-indicator {
        width: 7px;
        height: 7px;
        border-radius: 50%;
    }
    .order-summary-panel {
        position: sticky;
        top: 86px;
        border-radius: 16px;
        background: #ffffff;
        border: 1px solid #edf2f7;
        box-shadow: 0 4px 20px rgba(0,0,0,0.03);
        padding: 1.35rem;
    }
    .cart-item-row {
        padding: 0.75rem 0;
        border-bottom: 1px dashed #e2e8f0;
    }
    .qty-btn {
        width: 26px;
        height: 26px;
        border-radius: 8px;
        border: 1px solid #cbd5e1;
        background: #ffffff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.85rem;
        font-weight: bold;
        color: #1e293b;
        cursor: pointer;
        transition: all 0.15s ease;
    }
    .qty-btn:hover:not(:disabled) {
        background: #111827;
        color: #ffffff;
        border-color: #111827;
    }
    .qty-btn:disabled {
        opacity: 0.4;
        cursor: not-allowed;
    }
</style>

<div class="row g-4">
    <!-- Kolom Kiri: Katalog Kasir POS (8 Kolom) -->
    <div class="col-lg-8">
        <!-- Category Pills & Search Bar Header -->
        <div class="card border-0 shadow-sm rounded-4 p-3 mb-4 bg-white">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                <!-- Category Pills (Horizontal Scrollable) -->
                <div class="d-flex align-items-center gap-2 overflow-x-auto pb-1" style="max-width: 100%;">
                    <a href="javascript:void(0)" onclick="filterCategory('')" id="cat-pill-all" class="category-pill active">
                        <i class="bi bi-grid-fill small"></i>
                        <span>All</span>
                        <span class="category-badge-count bg-white text-dark fw-bold">{{ $totalAllProducts }}</span>
                    </a>
                    @foreach($categories as $category)
                        <a href="javascript:void(0)" onclick="filterCategory('{{ $category->id }}')" id="cat-pill-{{ $category->id }}" class="category-pill inactive">
                            <span>{{ $category->name }}</span>
                            <span class="category-badge-count bg-light text-secondary border">{{ $category->products_count }}</span>
                        </a>
                    @endforeach
                </div>

                <!-- Search & Refresh -->
                <div class="d-flex align-items-center gap-2 ms-auto">
                    <button type="button" class="btn btn-light rounded-pill border px-3 py-2 text-secondary d-flex align-items-center gap-1" onclick="resetFilters()" title="Refresh Menu">
                        <i class="bi bi-arrow-clockwise"></i>
                        <span class="small d-none d-md-inline">Refresh</span>
                    </button>
                    <div class="input-group" style="width: 240px;">
                        <span class="input-group-text bg-white border-end-0 rounded-start-pill text-muted ps-3">
                            <i class="bi bi-search"></i>
                        </span>
                        <input type="text" id="catalog-search" class="form-control border-start-0 rounded-end-pill py-2 pe-3" placeholder="Search product or SKU..." onkeyup="filterSearch()">
                    </div>
                </div>
            </div>
        </div>

        <!-- Product Grid (Responsive Cards) -->
        <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 g-3 mb-4" id="products-grid">
            @forelse($products as $product)
            <div class="col product-item" data-category-id="{{ $product->category_id }}" data-name="{{ strtolower($product->name) }}" data-sku="{{ strtolower($product->sku) }}">
                <div class="product-card h-100 p-3 d-flex flex-column">
                    <!-- Product Image & Floating Status -->
                    <div class="product-img-wrapper mb-3">
                        <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="product-img">
                        <div class="status-floating-pill">
                            @if($product->stock > 0)
                                <span class="dot-indicator bg-success"></span>
                                <span class="text-success">Available ({{ $product->stock }})</span>
                            @else
                                <span class="dot-indicator bg-danger"></span>
                                <span class="text-danger">Not Available</span>
                            @endif
                        </div>
                    </div>

                    <!-- Product Details -->
                    <div class="d-flex justify-content-between align-items-start mb-1">
                        <h6 class="fw-bold text-dark mb-0 text-truncate me-2" title="{{ $product->name }}" style="max-width: 65%;">
                            {{ $product->name }}
                        </h6>
                        <div class="fw-bold text-dark text-nowrap">
                            Rp {{ number_format($product->selling_price, 0, ',', '.') }}
                        </div>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <code class="small text-muted">{{ $product->sku }}</code>
                        <span class="badge bg-light text-secondary border small" style="font-size: 0.7rem;">
                            {{ $product->category->name ?? 'Running' }}
                        </span>
                    </div>

                    <!-- Action Button -->
                    <div class="mt-auto">
                        @if($product->stock > 0)
                            <button type="button" 
                                    id="btn-add-{{ $product->id }}" 
                                    class="btn btn-pos-dark w-100 py-2 d-flex align-items-center justify-content-center gap-1"
                                    onclick="addToCart({{ $product->id }}, '{{ addslashes($product->name) }}', '{{ $product->sku }}', {{ $product->selling_price }}, {{ $product->stock }}, '{{ $product->image_url }}')">
                                <i class="bi bi-plus-lg"></i>
                                <span>Add to Cart</span>
                            </button>
                        @else
                            <button type="button" class="btn btn-light text-muted w-100 py-2 border" disabled>
                                <i class="bi bi-x-circle me-1"></i> Not Available
                            </button>
                        @endif
                    </div>
                </div>
            </div>
            @empty
            <div class="col-12 w-100">
                <div class="card border-0 shadow-sm py-5 text-center rounded-4">
                    <i class="bi bi-box-seam fs-1 d-block mb-2 text-muted"></i>
                    <h5 class="fw-bold">Tidak ada produk dengan stok tersedia</h5>
                    <p class="text-muted">Silakan lakukan pencatatan stok masuk (Purchases) terlebih dahulu.</p>
                </div>
            </div>
            @endforelse
        </div>

        <div id="no-filter-results" class="card border-0 shadow-sm py-5 text-center rounded-4 d-none">
            <i class="bi bi-search fs-1 d-block mb-2 text-muted"></i>
            <h5 class="fw-bold">Produk Tidak Ditemukan</h5>
            <p class="text-muted small">Coba ubah kata kunci pencarian atau pilih kategori lain.</p>
            <button class="btn btn-outline-dark btn-sm mx-auto" onclick="resetFilters()">Reset Filter</button>
        </div>
    </div>

    <!-- Kolom Kanan: Order Summary POS (4 Kolom) -->
    <div class="col-lg-4">
        <div class="order-summary-panel">
            <div class="d-flex justify-content-between align-items-center pb-3 border-bottom mb-3">
                <h5 class="fw-bold text-dark mb-0">Order Summary</h5>
                <span class="badge bg-light text-muted border font-monospace" id="order-ref-code">#INV-{{ strtoupper(substr(uniqid(), -6)) }}</span>
            </div>

            <!-- Tanggal Penjualan Kasir -->
            <div class="mb-3">
                <label class="form-label small text-muted mb-1 fw-semibold">Tanggal Transaksi Kasir:</label>
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-light"><i class="bi bi-calendar-event"></i></span>
                    <input type="date" form="checkout-form" name="sold_at" class="form-control" value="{{ date('Y-m-d') }}" required>
                </div>
            </div>

            <!-- List Cart Items -->
            <div id="cart-items-container" style="max-height: 340px; overflow-y: auto; padding-right: 2px;">
                <div id="cart-empty-state" class="py-5 text-center text-muted">
                    <i class="bi bi-bag-plus fs-1 d-block mb-2 text-secondary opacity-50"></i>
                    <p class="fw-semibold mb-1">Kasir belum memilih produk</p>
                    <small class="text-muted">Klik produk pada katalog untuk menambahkan ke nota kasir.</small>
                </div>
            </div>

            <!-- Price Breakdown -->
            <div class="pt-3 border-top mt-3">
                <div class="d-flex justify-content-between align-items-center mb-2 small text-muted">
                    <span>Subtotal</span>
                    <span class="fw-semibold text-dark" id="summary-subtotal">Rp 0</span>
                </div>
                <div class="d-flex justify-content-between align-items-center mb-2 small text-muted">
                    <span>Total Item Terjual</span>
                    <span class="fw-semibold text-dark" id="summary-total-items">0 item</span>
                </div>
                <div class="d-flex justify-content-between align-items-center pt-2 border-top mb-3">
                    <span class="fw-bold text-dark fs-6">Total Tagihan</span>
                    <span class="fw-bold text-dark fs-5" id="summary-total-payment">Rp 0</span>
                </div>

                <!-- Pilihan Metode Pembayaran -->
                <div class="mb-3">
                    <label class="form-label small text-muted mb-2 fw-semibold d-block">Metode Pembayaran:</label>
                    <div class="row g-2">
                        <div class="col-6">
                            <input type="radio" class="btn-check" name="payment_method_radio" id="pay_cash" value="cash" checked onchange="onPaymentMethodChange('cash')">
                            <label class="btn btn-outline-dark w-100 py-2 d-flex flex-column align-items-center justify-content-center gap-1 rounded-3 small" for="pay_cash">
                                <i class="bi bi-cash-coin fs-5"></i>
                                <span class="fw-semibold" style="font-size: 0.78rem;">Cash / Tunai</span>
                            </label>
                        </div>
                        <div class="col-6">
                            <input type="radio" class="btn-check" name="payment_method_radio" id="pay_qris" value="qris" onchange="onPaymentMethodChange('qris')">
                            <label class="btn btn-outline-dark w-100 py-2 d-flex flex-column align-items-center justify-content-center gap-1 rounded-3 small" for="pay_qris">
                                <i class="bi bi-qr-code-scan fs-5"></i>
                                <span class="fw-semibold" style="font-size: 0.78rem;">QRIS</span>
                            </label>
                        </div>
                        <div class="col-6">
                            <input type="radio" class="btn-check" name="payment_method_radio" id="pay_transfer" value="transfer" onchange="onPaymentMethodChange('transfer')">
                            <label class="btn btn-outline-dark w-100 py-2 d-flex flex-column align-items-center justify-content-center gap-1 rounded-3 small" for="pay_transfer">
                                <i class="bi bi-bank fs-5"></i>
                                <span class="fw-semibold" style="font-size: 0.78rem;">Bank Transfer</span>
                            </label>
                        </div>
                        <div class="col-6">
                            <input type="radio" class="btn-check" name="payment_method_radio" id="pay_card" value="card" onchange="onPaymentMethodChange('card')">
                            <label class="btn btn-outline-dark w-100 py-2 d-flex flex-column align-items-center justify-content-center gap-1 rounded-3 small" for="pay_card">
                                <i class="bi bi-credit-card fs-5"></i>
                                <span class="fw-semibold" style="font-size: 0.78rem;">Debit / Card</span>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Checkout Form -->
                <form action="{{ route('sales.store') }}" method="POST" id="checkout-form">
                    @csrf
                    <input type="hidden" name="redirect_to_receipt" value="1">
                    <input type="hidden" name="payment_method" id="form_payment_method" value="cash">
                    <!-- Dynamic product inputs generated via JS -->
                    <div id="dynamic-form-inputs"></div>

                    <button type="button" id="btn-confirm-checkout" class="btn btn-pos-dark w-100 py-3 d-flex align-items-center justify-content-center gap-2 fs-6 shadow-sm" onclick="openPaymentModal()" disabled>
                        <i class="bi bi-receipt"></i>
                        <span>Confirm Payment & Print Nota</span>
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal Konfirmasi Pembayaran -->
<div class="modal fade" id="paymentConfirmationModal" tabindex="-1" aria-labelledby="paymentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow rounded-4 overflow-hidden">
            <div class="modal-header bg-dark text-white border-0 py-3">
                <div class="d-flex align-items-center gap-2">
                    <i class="bi bi-shield-check fs-4 text-success"></i>
                    <div>
                        <h5 class="modal-title fw-bold mb-0" id="paymentModalLabel">Konfirmasi Pembayaran POS</h5>
                        <small class="text-white-50">RunnersVault Kasir & Inventaris</small>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <!-- Total Tagihan Banner -->
                <div class="bg-light p-3 rounded-3 border text-center mb-4">
                    <div class="text-muted small mb-1">Total Tagihan Yang Harus Dibayar</div>
                    <div class="fw-bold text-dark fs-3" id="modal-total-tagihan">Rp 0</div>
                    <div class="mt-2">
                        <span class="badge bg-dark rounded-pill px-3 py-1" id="modal-selected-method-badge">
                            <i class="bi bi-cash-coin me-1"></i> Cash / Tunai
                        </span>
                    </div>
                </div>

                <!-- Tab 1: Cash / Tunai -->
                <div id="modal-pay-section-cash" class="pay-section">
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Uang Diterima dari Pelanggan (Rp):</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white fw-bold">Rp</span>
                            <input type="number" id="cash-received-input" class="form-control form-control-lg fw-bold" placeholder="0" min="0" oninput="calculateChange()">
                        </div>
                    </div>
                    <!-- Quick Cash Buttons -->
                    <div class="d-flex flex-wrap gap-2 mb-3">
                        <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill" onclick="setCashAmount('exact')">Uang Pas</button>
                        <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill" onclick="setCashAmount(50000)">50.000</button>
                        <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill" onclick="setCashAmount(100000)">100.000</button>
                        <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill" onclick="setCashAmount(200000)">200.000</button>
                        <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill" onclick="setCashAmount(500000)">500.000</button>
                    </div>
                    <!-- Kembalian Display -->
                    <div class="p-3 rounded-3 border bg-success-subtle border-success-subtle">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="fw-semibold text-success-emphasis">Kembalian:</span>
                            <span class="fs-5 fw-bold text-success-emphasis" id="cash-change-display">Rp 0</span>
                        </div>
                        <small id="cash-warning" class="text-danger d-none mt-1 d-block fw-semibold">
                            <i class="bi bi-exclamation-circle me-1"></i> Uang yang diterima kurang dari total tagihan!
                        </small>
                    </div>
                </div>

                <!-- Tab 2: QRIS -->
                <div id="modal-pay-section-qris" class="pay-section d-none">
                    <div class="text-center p-3 border rounded-3 bg-white">
                        <div class="d-inline-block p-3 border rounded-3 shadow-sm bg-white mb-2">
                            <svg width="180" height="180" viewBox="0 0 200 200" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <rect width="200" height="200" fill="white"/>
                                <rect x="20" y="20" width="50" height="50" rx="6" stroke="#111" stroke-width="8" fill="white"/>
                                <rect x="33" y="33" width="24" height="24" fill="#111"/>
                                <rect x="130" y="20" width="50" height="50" rx="6" stroke="#111" stroke-width="8" fill="white"/>
                                <rect x="143" y="33" width="24" height="24" fill="#111"/>
                                <rect x="20" y="130" width="50" height="50" rx="6" stroke="#111" stroke-width="8" fill="white"/>
                                <rect x="33" y="143" width="24" height="24" fill="#111"/>
                                <rect x="90" y="25" width="12" height="40" fill="#111"/>
                                <rect x="110" y="40" width="10" height="15" fill="#111"/>
                                <rect x="25" y="90" width="15" height="15" fill="#111"/>
                                <rect x="50" y="85" width="20" height="12" fill="#111"/>
                                <rect x="85" y="85" width="30" height="30" rx="4" fill="#10b981"/>
                                <path d="M95 100 L100 105 L110 95" stroke="white" stroke-width="3" stroke-linecap="round"/>
                                <rect x="130" y="90" width="20" height="12" fill="#111"/>
                                <rect x="160" y="85" width="15" height="20" fill="#111"/>
                                <rect x="85" y="135" width="20" height="20" fill="#111"/>
                                <rect x="115" y="130" width="20" height="12" fill="#111"/>
                                <rect x="145" y="145" width="30" height="30" fill="#111"/>
                                <rect x="120" y="160" width="15" height="15" fill="#111"/>
                            </svg>
                        </div>
                        <div class="fw-bold text-dark">NMID: ID1029384756182</div>
                        <small class="text-muted d-block">Scan QRIS menggunakan BCA, Mandiri, GoPay, OVO, ShopeePay, atau DANA.</small>
                        <div class="badge bg-success-subtle text-success border border-success-subtle mt-2 px-3 py-1">
                            <i class="bi bi-clock me-1"></i> Menunggu Scan Kasir...
                        </div>
                    </div>
                </div>

                <!-- Tab 3: Bank Transfer -->
                <div id="modal-pay-section-transfer" class="pay-section d-none">
                    <div class="d-flex flex-column gap-2.5">
                        <div class="p-3 border rounded-3 bg-light d-flex justify-content-between align-items-center">
                            <div>
                                <small class="text-muted d-block">BCA Virtual Account</small>
                                <strong class="font-monospace fs-6 text-dark">8801 2938 4710 2938</strong>
                                <small class="d-block text-secondary">a.n RunnersVault Store</small>
                            </div>
                            <span class="badge bg-primary">BCA</span>
                        </div>
                        <div class="p-3 border rounded-3 bg-light d-flex justify-content-between align-items-center">
                            <div>
                                <small class="text-muted d-block">Mandiri Virtual Account</small>
                                <strong class="font-monospace fs-6 text-dark">8910 0827 3649 1029</strong>
                                <small class="d-block text-secondary">a.n RunnersVault Official</small>
                            </div>
                            <span class="badge bg-warning text-dark">MANDIRI</span>
                        </div>
                        <small class="text-muted text-center d-block">
                            <i class="bi bi-info-circle me-1"></i> Pembayaran melalui Virtual Account akan diverifikasi otomatis oleh sistem.
                        </small>
                    </div>
                </div>

                <!-- Tab 4: Debit / Credit Card -->
                <div id="modal-pay-section-card" class="pay-section d-none">
                    <div class="p-3 border rounded-3 bg-light mb-3">
                        <div class="mb-2">
                            <label class="form-label small text-muted mb-1">Nomor Kartu Debit/Kredit (Mesin EDC):</label>
                            <div class="input-group input-group-sm">
                                <span class="input-group-text bg-white"><i class="bi bi-credit-card-2-front"></i></span>
                                <input type="text" class="form-control font-monospace" placeholder="4111 •••• •••• 1234" value="4532 •••• •••• 8821">
                            </div>
                        </div>
                        <div class="row g-2">
                            <div class="col-6">
                                <label class="form-label small text-muted mb-1">Valid Thru:</label>
                                <input type="text" class="form-control form-control-sm font-monospace" placeholder="MM/YY" value="12/28">
                            </div>
                            <div class="col-6">
                                <label class="form-label small text-muted mb-1">Tipe Kartu:</label>
                                <select class="form-select form-select-sm">
                                    <option>Visa / Mastercard</option>
                                    <option>GPN Debit</option>
                                    <option>BCA Card</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="badge bg-info-subtle text-info-emphasis border border-info-subtle w-100 py-2 d-flex align-items-center justify-content-center gap-1">
                        <i class="bi bi-check2-circle"></i> Mesin EDC Siap Menerima Pembayaran
                    </div>
                </div>
            </div>
            <div class="modal-footer bg-light border-0 py-3 d-flex justify-content-between">
                <button type="button" class="btn btn-outline-secondary px-4 rounded-pill" data-bs-dismiss="modal">
                    Batal
                </button>
                <button type="button" class="btn btn-pos-dark px-4 rounded-pill d-flex align-items-center gap-2 fw-semibold" onclick="submitFinalPayment()">
                    <i class="bi bi-printer"></i>
                    <span>Selesaikan Pembayaran & Cetak Struk</span>
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    let cart = {}; // key: productId -> { id, name, sku, price, stock, image, qty }
    let currentCategoryId = '';
    let currentTotalPayment = 0;
    let selectedPaymentMethod = 'cash';

    function formatRupiah(amount) {
        return 'Rp ' + amount.toLocaleString('id-ID');
    }

    function onPaymentMethodChange(method) {
        selectedPaymentMethod = method;
        document.getElementById('form_payment_method').value = method;
        updatePaymentModalView();
    }

    function updatePaymentModalView() {
        const badge = document.getElementById('modal-selected-method-badge');
        const sections = ['cash', 'qris', 'transfer', 'card'];
        
        sections.forEach(sec => {
            const el = document.getElementById('modal-pay-section-' + sec);
            if (el) {
                if (sec === selectedPaymentMethod) {
                    el.classList.remove('d-none');
                } else {
                    el.classList.add('d-none');
                }
            }
        });

        if (badge) {
            const map = {
                'cash': '<i class="bi bi-cash-coin me-1"></i> Cash / Tunai',
                'qris': '<i class="bi bi-qr-code-scan me-1"></i> QRIS',
                'transfer': '<i class="bi bi-bank me-1"></i> Bank Transfer',
                'card': '<i class="bi bi-credit-card me-1"></i> Debit / Card'
            };
            badge.innerHTML = map[selectedPaymentMethod] || map['cash'];
        }

        if (selectedPaymentMethod === 'cash') {
            const cashInput = document.getElementById('cash-received-input');
            if (cashInput && (!cashInput.value || Number(cashInput.value) === 0)) {
                cashInput.value = currentTotalPayment;
            }
            calculateChange();
        }
    }

    function openPaymentModal() {
        if (Object.keys(cart).length === 0) return;
        
        document.getElementById('modal-total-tagihan').innerText = formatRupiah(currentTotalPayment);
        updatePaymentModalView();
        
        const modalEl = document.getElementById('paymentConfirmationModal');
        const modal = new bootstrap.Modal(modalEl);
        modal.show();
    }

    function setCashAmount(val) {
        const cashInput = document.getElementById('cash-received-input');
        if (!cashInput) return;
        if (val === 'exact') {
            cashInput.value = currentTotalPayment;
        } else {
            cashInput.value = val;
        }
        calculateChange();
    }

    function calculateChange() {
        const cashInput = document.getElementById('cash-received-input');
        const changeDisplay = document.getElementById('cash-change-display');
        const warning = document.getElementById('cash-warning');
        if (!cashInput || !changeDisplay) return;

        const received = Number(cashInput.value) || 0;
        const change = received - currentTotalPayment;

        if (change >= 0) {
            changeDisplay.innerText = formatRupiah(change);
            changeDisplay.classList.remove('text-danger');
            changeDisplay.classList.add('text-success-emphasis');
            if (warning) warning.classList.add('d-none');
        } else {
            changeDisplay.innerText = '- ' + formatRupiah(Math.abs(change));
            changeDisplay.classList.add('text-danger');
            changeDisplay.classList.remove('text-success-emphasis');
            if (warning) warning.classList.remove('d-none');
        }
    }

    function submitFinalPayment() {
        document.getElementById('checkout-form').submit();
    }

    function addToCart(id, name, sku, price, maxStock, image) {
        if (!cart[id]) {
            cart[id] = { id, name, sku, price, stock: maxStock, image, qty: 1 };
        } else {
            if (cart[id].qty < maxStock) {
                cart[id].qty += 1;
            }
        }
        renderCart();
    }

    function updateQty(id, delta) {
        if (!cart[id]) return;
        const newQty = cart[id].qty + delta;
        if (newQty <= 0) {
            delete cart[id];
        } else if (newQty <= cart[id].stock) {
            cart[id].qty = newQty;
        }
        renderCart();
    }

    function removeFromCart(id) {
        if (cart[id]) {
            delete cart[id];
            renderCart();
        }
    }

    function renderCart() {
        const container = document.getElementById('cart-items-container');
        const emptyState = document.getElementById('cart-empty-state');
        const inputsContainer = document.getElementById('dynamic-form-inputs');
        const confirmBtn = document.getElementById('btn-confirm-checkout');

        const keys = Object.keys(cart);

        // Update all product card button states
        document.querySelectorAll('[id^="btn-add-"]').forEach(btn => {
            const pId = btn.id.replace('btn-add-', '');
            if (cart[pId]) {
                btn.classList.remove('btn-pos-dark');
                btn.classList.add('btn-outline-dark', 'fw-bold');
                btn.innerHTML = `<i class="bi bi-check2"></i> Add More (${cart[pId].qty})`;
            } else {
                btn.classList.remove('btn-outline-dark');
                btn.classList.add('btn-pos-dark');
                btn.innerHTML = `<i class="bi bi-plus-lg"></i> Add to Cart`;
            }
        });

        if (keys.length === 0) {
            currentTotalPayment = 0;
            container.innerHTML = `
                <div id="cart-empty-state" class="py-5 text-center text-muted">
                    <i class="bi bi-bag-plus fs-1 d-block mb-2 text-secondary opacity-50"></i>
                    <p class="fw-semibold mb-1">Kasir belum memilih produk</p>
                    <small class="text-muted">Klik produk pada katalog untuk menambahkan ke nota kasir.</small>
                </div>
            `;
            inputsContainer.innerHTML = '';
            document.getElementById('summary-subtotal').innerText = 'Rp 0';
            document.getElementById('summary-total-items').innerText = '0 item';
            document.getElementById('summary-total-payment').innerText = 'Rp 0';
            confirmBtn.disabled = true;
            return;
        }

        let html = '';
        let inputsHtml = '';
        let totalPayment = 0;
        let totalItems = 0;

        keys.forEach((id, index) => {
            const item = cart[id];
            const subtotal = item.qty * item.price;
            totalPayment += subtotal;
            totalItems += item.qty;

            html += `
                <div class="cart-item-row d-flex align-items-center gap-3">
                    <img src="${item.image}" alt="${item.name}" class="rounded-3 border shadow-sm flex-shrink-0" style="width: 48px; height: 48px; object-fit: cover;">
                    <div class="flex-grow-1 overflow-hidden">
                        <div class="d-flex justify-content-between align-items-start">
                            <h6 class="fw-bold text-dark mb-0 small text-truncate" title="${item.name}" style="max-width: 140px;">
                                ${item.name}
                            </h6>
                            <button type="button" class="btn btn-link text-danger p-0 ms-2" onclick="removeFromCart(${item.id})" title="Hapus">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                        <div class="text-muted small">${formatRupiah(item.price)} × ${item.qty}</div>
                        <div class="d-flex justify-content-between align-items-center mt-1">
                            <span class="fw-bold text-primary small">${formatRupiah(subtotal)}</span>
                            <div class="d-flex align-items-center gap-1">
                                <button type="button" class="qty-btn" onclick="updateQty(${item.id}, -1)">-</button>
                                <span class="px-2 small fw-bold">${item.qty}</span>
                                <button type="button" class="qty-btn" onclick="updateQty(${item.id}, 1)" ${item.qty >= item.stock ? 'disabled' : ''}>+</button>
                            </div>
                        </div>
                    </div>
                </div>
            `;

            inputsHtml += `
                <input type="hidden" name="products[${index}][id]" value="${item.id}">
                <input type="hidden" name="products[${index}][quantity]" value="${item.qty}">
            `;
        });

        container.innerHTML = html;
        inputsContainer.innerHTML = inputsHtml;

        currentTotalPayment = totalPayment;
        document.getElementById('summary-subtotal').innerText = formatRupiah(totalPayment);
        document.getElementById('summary-total-items').innerText = `${totalItems} item`;
        document.getElementById('summary-total-payment').innerText = formatRupiah(totalPayment);
        confirmBtn.disabled = false;
    }

    // Client-side Filtering
    function filterCategory(categoryId) {
        currentCategoryId = categoryId;

        document.querySelectorAll('.category-pill').forEach(pill => {
            pill.classList.remove('active');
            pill.classList.add('inactive');
        });

        if (categoryId === '') {
            document.getElementById('cat-pill-all').classList.add('active');
            document.getElementById('cat-pill-all').classList.remove('inactive');
        } else {
            const el = document.getElementById(`cat-pill-${categoryId}`);
            if (el) {
                el.classList.add('active');
                el.classList.remove('inactive');
            }
        }

        applyFilters();
    }

    function filterSearch() {
        applyFilters();
    }

    function applyFilters() {
        const query = document.getElementById('catalog-search').value.toLowerCase().trim();
        let visibleCount = 0;

        document.querySelectorAll('.product-item').forEach(item => {
            const itemCat = item.getAttribute('data-category-id');
            const itemName = item.getAttribute('data-name');
            const itemSku = item.getAttribute('data-sku');

            const matchesCategory = (currentCategoryId === '' || itemCat === currentCategoryId);
            const matchesSearch = (query === '' || itemName.includes(query) || itemSku.includes(query));

            if (matchesCategory && matchesSearch) {
                item.style.display = '';
                visibleCount++;
            } else {
                item.style.display = 'none';
            }
        });

        const noResults = document.getElementById('no-filter-results');
        if (visibleCount === 0) {
            noResults.classList.remove('d-none');
        } else {
            noResults.classList.add('d-none');
        }
    }

    function resetFilters() {
        document.getElementById('catalog-search').value = '';
        filterCategory('');
    }
</script>
@endsection
