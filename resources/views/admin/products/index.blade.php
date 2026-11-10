@extends('layouts.admin')

@section('title', 'Products')

@section('content')

<div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
  <form method="GET" action="{{ route('admin.products.index') }}" class="d-flex gap-2 flex-wrap admin-form">
    <input type="text" name="q" value="{{ $filters['q'] }}" class="form-control form-control-sm" placeholder="Search name or SKU" style="width:220px">
    <select name="category" class="form-select form-select-sm" style="width:180px" onchange="this.form.submit()">
      <option value="">All categories</option>
      @foreach($categories as $category)
        <option value="{{ $category->id }}" {{ (string) $filters['category'] === (string) $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
      @endforeach
    </select>
    <select name="status" class="form-select form-select-sm" style="width:150px" onchange="this.form.submit()">
      <option value="">All status</option>
      <option value="active" {{ $filters['status'] === 'active' ? 'selected' : '' }}>Active</option>
      <option value="inactive" {{ $filters['status'] === 'inactive' ? 'selected' : '' }}>Inactive</option>
    </select>
    <button class="btn btn-ghost btn-sm" type="submit">Filter</button>
  </form>
  <a href="{{ route('admin.products.create') }}" class="btn btn-gold btn-sm">+ New Product</a>
</div>

<div class="admin-card">
  <div style="overflow-x:auto">
    <table class="admin-table">
      <thead><tr><th>Name</th><th>SKU</th><th>Category</th><th>Price</th><th>Stock</th><th>Status</th><th></th></tr></thead>
      <tbody>
        @forelse($products as $product)
          <tr>
            <td><a href="{{ route('admin.products.edit', $product) }}" class="text-gold">{{ $product->name }}</a></td>
            <td>{{ $product->sku }}</td>
            <td>{{ $product->category->name ?? '—' }}</td>
            <td>${{ number_format((float) $product->price) }}</td>
            <td>{{ $product->stock_quantity }}</td>
            <td>
              <form method="POST" action="{{ route('admin.products.toggle-active', $product) }}" class="d-inline">
                @csrf @method('PATCH')
                <button type="submit" class="admin-badge {{ $product->is_active ? 'admin-badge-active' : 'admin-badge-inactive' }}" style="border:1px solid var(--line);background:transparent">
                  {{ $product->is_active ? 'Active' : 'Inactive' }}
                </button>
              </form>
            </td>
            <td>
              <a href="{{ route('admin.products.edit', $product) }}" class="text-gold me-2" style="font-size:.78rem">Edit</a>
              <form method="POST" action="{{ route('admin.products.destroy', $product) }}" class="d-inline" onsubmit="return confirm('Remove this product?');">
                @csrf @method('DELETE')
                <button type="submit" class="text-muted-lux border-0 bg-transparent p-0" style="font-size:.78rem">Delete</button>
              </form>
            </td>
          </tr>
        @empty
          <tr><td colspan="7" class="text-muted-lux text-center py-4">No products found.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>

<div class="mt-3">{{ $products->links('pagination::bootstrap-5') }}</div>

@endsection
