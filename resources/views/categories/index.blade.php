@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="fw-bold mb-0"><i class="bi bi-tags me-2"></i>Kelola Kategori Produk</h3>
    <a href="{{ route('categories.create') }}" class="btn btn-dark"><i class="bi bi-plus-lg me-1"></i> Tambah Kategori</a>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>Nama Kategori</th>
                    <th>Jumlah Produk</th>
                    <th class="text-end">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($categories as $index => $category)
                <tr>
                    <td>{{ $categories->firstItem() + $index }}</td>
                    <td class="fw-semibold">{{ $category->name }}</td>
                    <td><span class="badge bg-secondary">{{ $category->products_count }} produk</span></td>
                    <td class="text-end">
                        <a href="{{ route('categories.edit', $category->id) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                        <form action="{{ route('categories.destroy', $category->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus kategori ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger">Hapus</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="text-center py-4 text-muted">Belum ada kategori.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-4">
    {{ $categories->links() }}
</div>
@endsection

