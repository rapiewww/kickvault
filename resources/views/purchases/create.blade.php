@extends('layouts.app')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-9">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <h4 class="fw-bold mb-4">Form Transaksi Pembelian (Restock Barang)</h4>

                <form action="{{ route('purchases.store') }}" method="POST">
                    @csrf
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Pilih Supplier</label>
                            <select name="supplier_id" class="form-select" required>
                                <option value="">-- Pilih Supplier --</option>
                                @foreach($suppliers as $supplier)
                                    <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Tanggal Pembelian</label>
                            <input type="date" name="purchased_at" class="form-control" value="{{ date('Y-m-d') }}" required>
                        </div>
                    </div>

                    <hr class="my-4">
                    <h5 class="fw-bold mb-3">Daftar Produk yang Dibeli</h5>

                    <div id="product-rows">
                        <div class="row g-2 mb-3 product-row align-items-end">
                            <div class="col-md-5">
                                <label class="form-label small">Produk</label>
                                <select name="products[0][id]" class="form-select" required>
                                    <option value="">-- Pilih Produk --</option>
                                    @foreach($products as $product)
                                        <option value="{{ $product->id }}">{{ $product->name }} (Stok: {{ $product->stock }})</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label small">Jumlah (Qty)</label>
                                <input type="number" name="products[0][quantity]" class="form-control" min="1" value="1" required>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label small">Harga Beli Satuan (Rp)</label>
                                <input type="number" name="products[0][unit_cost]" class="form-control" min="0" required>
                            </div>
                            <div class="col-md-1">
                                <button type="button" class="btn btn-outline-danger btn-sm remove-row w-100" style="display:none;">X</button>
                            </div>
                        </div>
                    </div>

                    <button type="button" id="add-row" class="btn btn-outline-dark btn-sm mb-4">+ Tambah Baris Produk</button>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('purchases.index') }}" class="btn btn-secondary">Kembali</a>
                        <button type="submit" class="btn btn-dark">Simpan Pembelian & Tambah Stok</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    let rowIndex = 1;
    document.getElementById('add-row').addEventListener('click', function () {
        let container = document.getElementById('product-rows');
        let firstRow = container.querySelector('.product-row');
        let newRow = firstRow.cloneNode(true);
        
        // Update name attributes index
        newRow.querySelectorAll('select, input').forEach(element => {
            let name = element.getAttribute('name');
            element.value = '';
            element.setAttribute('name', name.replace(/\[\d+\]/, `[${rowIndex}]`));
        });

        newRow.querySelector('.remove-row').style.display = 'block';
        container.appendChild(newRow);
        rowIndex++;
    });

    document.addEventListener('click', function (e) {
        if (e.target && e.target.classList.contains('remove-row')) {
            e.target.closest('.product-row').remove();
        }
    });
</script>
@endsection
