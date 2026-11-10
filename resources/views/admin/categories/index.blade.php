@extends('layouts.admin')

@section('title', 'Categories')

@section('content')

<div class="d-flex justify-content-end mb-3">
  <a href="{{ route('admin.categories.create') }}" class="btn btn-gold btn-sm">+ New Category</a>
</div>

<div class="admin-card">
  <div style="overflow-x:auto">
    <table class="admin-table">
      <thead><tr><th>Name</th><th>Slug</th><th>Products</th><th>Status</th><th>Sort</th><th></th></tr></thead>
      <tbody>
        @foreach($categories as $category)
          <tr>
            <td><a href="{{ route('admin.categories.edit', $category) }}" class="text-gold">{{ $category->name }}</a></td>
            <td>{{ $category->slug }}</td>
            <td>{{ $category->products()->count() }}</td>
            <td><span class="admin-badge {{ $category->is_active ? 'admin-badge-active' : 'admin-badge-inactive' }}">{{ $category->is_active ? 'Active' : 'Inactive' }}</span></td>
            <td>{{ $category->sort_order }}</td>
            <td>
              <a href="{{ route('admin.categories.edit', $category) }}" class="text-gold me-2" style="font-size:.78rem">Edit</a>
              <form method="POST" action="{{ route('admin.categories.destroy', $category) }}" class="d-inline" onsubmit="return confirm('Remove this category?');">
                @csrf @method('DELETE')
                <button type="submit" class="text-muted-lux border-0 bg-transparent p-0" style="font-size:.78rem">Delete</button>
              </form>
            </td>
          </tr>
          @foreach($category->children as $child)
            <tr>
              <td class="ps-4">&#8627; <a href="{{ route('admin.categories.edit', $child) }}" class="text-gold">{{ $child->name }}</a></td>
              <td>{{ $child->slug }}</td>
              <td>{{ $child->products()->count() }}</td>
              <td><span class="admin-badge {{ $child->is_active ? 'admin-badge-active' : 'admin-badge-inactive' }}">{{ $child->is_active ? 'Active' : 'Inactive' }}</span></td>
              <td>{{ $child->sort_order }}</td>
              <td>
                <a href="{{ route('admin.categories.edit', $child) }}" class="text-gold me-2" style="font-size:.78rem">Edit</a>
                <form method="POST" action="{{ route('admin.categories.destroy', $child) }}" class="d-inline" onsubmit="return confirm('Remove this category?');">
                  @csrf @method('DELETE')
                  <button type="submit" class="text-muted-lux border-0 bg-transparent p-0" style="font-size:.78rem">Delete</button>
                </form>
              </td>
            </tr>
          @endforeach
        @endforeach
      </tbody>
    </table>
  </div>
</div>

@endsection
