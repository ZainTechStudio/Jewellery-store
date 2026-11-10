@extends('layouts.admin')

@section('title', 'Coupons')

@section('content')

<div class="d-flex justify-content-end mb-3">
  <a href="{{ route('admin.coupons.create') }}" class="btn btn-gold btn-sm">+ New Coupon</a>
</div>

<div class="admin-card">
  <div style="overflow-x:auto">
    <table class="admin-table">
      <thead><tr><th>Code</th><th>Type</th><th>Value</th><th>Usage</th><th>Expires</th><th>Status</th><th></th></tr></thead>
      <tbody>
        @forelse($coupons as $coupon)
          <tr>
            <td>{{ $coupon->code }}</td>
            <td>{{ ucfirst($coupon->type) }}</td>
            <td>{{ $coupon->type === 'percent' ? $coupon->value.'%' : '$'.number_format((float) $coupon->value) }}</td>
            <td>{{ $coupon->usage_count }}{{ $coupon->usage_limit ? ' / '.$coupon->usage_limit : '' }}</td>
            <td>{{ $coupon->expires_at?->format('M d, Y') ?? '—' }}</td>
            <td><span class="admin-badge {{ $coupon->is_active ? 'admin-badge-active' : 'admin-badge-inactive' }}">{{ $coupon->is_active ? 'Active' : 'Inactive' }}</span></td>
            <td>
              <a href="{{ route('admin.coupons.edit', $coupon) }}" class="text-gold me-2" style="font-size:.78rem">Edit</a>
              <form method="POST" action="{{ route('admin.coupons.destroy', $coupon) }}" class="d-inline" onsubmit="return confirm('Remove this coupon?');">
                @csrf @method('DELETE')
                <button type="submit" class="text-muted-lux border-0 bg-transparent p-0" style="font-size:.78rem">Delete</button>
              </form>
            </td>
          </tr>
        @empty
          <tr><td colspan="7" class="text-muted-lux text-center py-4">No coupons yet.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>

<div class="mt-3">{{ $coupons->links('pagination::bootstrap-5') }}</div>

@endsection
