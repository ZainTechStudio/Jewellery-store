@extends('layouts.admin')

@section('title', 'Customers')

@section('content')

<form method="GET" action="{{ route('admin.customers.index') }}" class="admin-form mb-3">
  <input type="text" name="q" value="{{ $filters['q'] }}" class="form-control form-control-sm" placeholder="Search name, email, phone" style="width:280px">
</form>

<div class="admin-card">
  <div style="overflow-x:auto">
    <table class="admin-table">
      <thead><tr><th>Name</th><th>Email</th><th>Phone</th><th>Role</th><th>Orders</th><th>Joined</th><th>Status</th><th></th></tr></thead>
      <tbody>
        @forelse($customers as $customer)
          <tr>
            <td><a href="{{ route('admin.customers.show', $customer) }}" class="text-gold">{{ $customer->name }}</a></td>
            <td>{{ $customer->email }}</td>
            <td>{{ $customer->phone ?? '—' }}</td>
            <td>{{ ucfirst($customer->role) }}</td>
            <td>{{ $customer->orders_count }}</td>
            <td>{{ $customer->created_at->format('M d, Y') }}</td>
            <td><span class="admin-badge {{ $customer->is_active ? 'admin-badge-active' : 'admin-badge-inactive' }}">{{ $customer->is_active ? 'Active' : 'Inactive' }}</span></td>
            <td>
              <form method="POST" action="{{ route('admin.customers.toggle-active', $customer) }}">
                @csrf @method('PATCH')
                <button type="submit" class="text-gold border-0 bg-transparent p-0" style="font-size:.78rem">{{ $customer->is_active ? 'Deactivate' : 'Activate' }}</button>
              </form>
            </td>
          </tr>
        @empty
          <tr><td colspan="8" class="text-muted-lux text-center py-4">No customers found.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>

<div class="mt-3">{{ $customers->links('pagination::bootstrap-5') }}</div>

@endsection
