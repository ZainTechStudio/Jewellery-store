@extends('layouts.admin')

@section('title', 'Reviews')

@section('content')

<form method="GET" action="{{ route('admin.reviews.index') }}" class="admin-form mb-3 d-flex gap-2">
  <select name="status" class="form-select form-select-sm" style="width:170px" onchange="this.form.submit()">
    <option value="">All reviews</option>
    <option value="approved" {{ $filters['status'] === 'approved' ? 'selected' : '' }}>Approved</option>
    <option value="pending" {{ $filters['status'] === 'pending' ? 'selected' : '' }}>Pending</option>
  </select>
</form>

<div class="admin-card">
  <div style="overflow-x:auto">
    <table class="admin-table">
      <thead><tr><th>Product</th><th>Customer</th><th>Rating</th><th>Title</th><th>Status</th><th>Date</th><th></th></tr></thead>
      <tbody>
        @forelse($reviews as $review)
          <tr>
            <td>{{ $review->product->name ?? '—' }}</td>
            <td>{{ $review->user->name ?? 'Guest' }}</td>
            <td>{{ $review->rating }}/5</td>
            <td>{{ $review->title ?? '—' }}</td>
            <td><span class="admin-badge {{ $review->is_approved ? 'admin-badge-active' : 'admin-badge-inactive' }}">{{ $review->is_approved ? 'Approved' : 'Pending' }}</span></td>
            <td>{{ $review->created_at->format('M d, Y') }}</td>
            <td class="d-flex gap-2">
              @unless($review->is_approved)
                <form method="POST" action="{{ route('admin.reviews.approve', $review) }}">@csrf @method('PATCH')<button class="text-gold border-0 bg-transparent p-0" style="font-size:.78rem">Approve</button></form>
              @else
                <form method="POST" action="{{ route('admin.reviews.reject', $review) }}">@csrf @method('PATCH')<button class="text-muted-lux border-0 bg-transparent p-0" style="font-size:.78rem">Hide</button></form>
              @endunless
              <form method="POST" action="{{ route('admin.reviews.destroy', $review) }}" onsubmit="return confirm('Delete this review?');">@csrf @method('DELETE')<button class="text-muted-lux border-0 bg-transparent p-0" style="font-size:.78rem">Delete</button></form>
            </td>
          </tr>
        @empty
          <tr><td colspan="7" class="text-muted-lux text-center py-4">No reviews yet.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>

<div class="mt-3">{{ $reviews->links('pagination::bootstrap-5') }}</div>

@endsection
