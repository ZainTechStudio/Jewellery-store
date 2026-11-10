@extends('layouts.admin')

@section('title', $coupon->exists ? 'Edit Coupon' : 'New Coupon')

@section('content')

<div class="row justify-content-center">
  <div class="col-lg-7">
    <div class="admin-card admin-form">
      <h2 class="h6 text-gold mb-3">{{ $coupon->exists ? 'Edit Coupon' : 'New Coupon' }}</h2>
      <p class="text-muted-lux mb-3" style="font-size:.78rem">Admin management only in this phase — coupons are not yet applied at checkout.</p>
      <form method="POST" action="{{ $coupon->exists ? route('admin.coupons.update', $coupon) : route('admin.coupons.store') }}">
        @csrf
        @if($coupon->exists) @method('PATCH') @endif

        <div class="row g-3">
          <div class="col-md-6"><label>Code</label><input type="text" name="code" value="{{ old('code', $coupon->code) }}" required class="form-control" style="text-transform:uppercase"></div>
          <div class="col-md-6"><label>Type</label>
            <select name="type" class="form-select">
              <option value="percent" {{ old('type', $coupon->type) === 'percent' ? 'selected' : '' }}>Percent</option>
              <option value="fixed" {{ old('type', $coupon->type) === 'fixed' ? 'selected' : '' }}>Fixed amount</option>
            </select>
          </div>
          <div class="col-md-6"><label>Value</label><input type="number" step="0.01" min="0" name="value" value="{{ old('value', $coupon->value) }}" required class="form-control"></div>
          <div class="col-md-6"><label>Minimum Order Amount</label><input type="number" step="0.01" min="0" name="minimum_order_amount" value="{{ old('minimum_order_amount', $coupon->minimum_order_amount) }}" class="form-control"></div>
          <div class="col-md-6"><label>Maximum Discount</label><input type="number" step="0.01" min="0" name="maximum_discount" value="{{ old('maximum_discount', $coupon->maximum_discount) }}" class="form-control"></div>
          <div class="col-md-6"><label>Usage Limit</label><input type="number" min="1" name="usage_limit" value="{{ old('usage_limit', $coupon->usage_limit) }}" class="form-control"></div>
          <div class="col-md-6"><label>Starts At</label><input type="date" name="starts_at" value="{{ old('starts_at', $coupon->starts_at?->format('Y-m-d')) }}" class="form-control"></div>
          <div class="col-md-6"><label>Expires At</label><input type="date" name="expires_at" value="{{ old('expires_at', $coupon->expires_at?->format('Y-m-d')) }}" class="form-control"></div>
          <div class="col-12 d-flex align-items-center gap-2">
            <input type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', $coupon->exists ? $coupon->is_active : true) ? 'checked' : '' }}>
            <label for="is_active" class="mb-0">Active</label>
          </div>
          <div class="col-12"><button class="btn btn-gold mt-2" type="submit">{{ $coupon->exists ? 'Save Changes' : 'Create Coupon' }}</button></div>
        </div>
      </form>
    </div>
  </div>
</div>

@endsection
