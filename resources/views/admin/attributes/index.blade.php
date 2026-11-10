@extends('layouts.admin')

@section('title', $meta['plural'])

@section('content')

<div class="d-flex justify-content-end mb-3">
  <a href="{{ route('admin.'.$meta['type'].'.create') }}" class="btn btn-gold btn-sm">+ New {{ $meta['label'] }}</a>
</div>

<div class="admin-card">
  <div style="overflow-x:auto">
    <table class="admin-table">
      <thead><tr><th>Name</th><th>Slug</th><th>Status</th><th>Sort</th><th></th></tr></thead>
      <tbody>
        @forelse($items as $item)
          <tr>
            <td><a href="{{ route('admin.'.$meta['type'].'.edit', $item->id) }}" class="text-gold">{{ $item->name }}</a></td>
            <td>{{ $item->slug }}</td>
            <td><span class="admin-badge {{ $item->is_active ? 'admin-badge-active' : 'admin-badge-inactive' }}">{{ $item->is_active ? 'Active' : 'Inactive' }}</span></td>
            <td>{{ $item->sort_order }}</td>
            <td>
              <a href="{{ route('admin.'.$meta['type'].'.edit', $item->id) }}" class="text-gold me-2" style="font-size:.78rem">Edit</a>
              <form method="POST" action="{{ route('admin.'.$meta['type'].'.destroy', $item->id) }}" class="d-inline" onsubmit="return confirm('Remove this {{ $meta['label'] }}?');">
                @csrf @method('DELETE')
                <button type="submit" class="text-muted-lux border-0 bg-transparent p-0" style="font-size:.78rem">Delete</button>
              </form>
            </td>
          </tr>
        @empty
          <tr><td colspan="5" class="text-muted-lux text-center py-4">No {{ strtolower($meta['plural']) }} yet.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>

@endsection
