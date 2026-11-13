@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="fw-bold mb-0"><i class="bi bi-truck me-2"></i>Kelola Data Supplier</h3>
    <a href="{{ route('suppliers.create') }}" class="btn btn-dark"><i class="bi bi-plus-lg me-1"></i> Tambah Supplier</a>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>Nama Supplier</th>
                    <th>No. Telepon</th>
                    <th>Email</th>
                    <th>Total Transaksi Masuk</th>
                    <th class="text-end">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($suppliers as $index => $supplier)
                <tr>
                    <td>{{ $suppliers->firstItem() + $index }}</td>
                    <td class="fw-semibold">{{ $supplier->name }}</td>
                    <td>{{ $supplier->phone }}</td>
                    <td>{{ $supplier->email ?? '-' }}</td>
                    <td><span class="badge bg-secondary">{{ $supplier->purchases_count }} kali</span></td>
                    <td class="text-end">
                        <a href="{{ route('suppliers.edit', $supplier->id) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                        <form action="{{ route('suppliers.destroy', $supplier->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus supplier ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger">Hapus</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center py-4 text-muted">Belum ada data supplier.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-4">
    {{ $suppliers->links() }}
</div>
@endsection
