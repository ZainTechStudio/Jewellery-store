@extends('layouts.admin')

@section('title', ($item->exists ? 'Edit ' : 'New ').$meta['label'])

@section('content')

<div class="row justify-content-center">
  <div class="col-lg-6">
    <div class="admin-card admin-form">
      <h2 class="h6 text-gold mb-3">{{ $item->exists ? 'Edit' : 'New' }} {{ $meta['label'] }}</h2>
      <form method="POST" action="{{ $item->exists ? route('admin.'.$meta['type'].'.update', $item->id) : route('admin.'.$meta['type'].'.store') }}">
        @csrf
        @if($item->exists) @method('PATCH') @endif

        <div class="row g-3">
          <div class="col-md-6"><label>Name</label><input type="text" name="name" value="{{ old('name', $item->name) }}" required class="form-control"></div>
          <div class="col-md-6"><label>Slug</label><input type="text" name="slug" value="{{ old('slug', $item->slug) }}" required class="form-control"></div>
          <div class="col-md-6"><label>Sort Order</label><input type="number" min="0" name="sort_order" value="{{ old('sort_order', $item->sort_order ?? 0) }}" class="form-control"></div>
          <div class="col-12"><label>Description</label><textarea name="description" rows="3" class="form-control">{{ old('description', $item->description) }}</textarea></div>
          <div class="col-12 d-flex align-items-center gap-2">
            <input type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', $item->exists ? $item->is_active : true) ? 'checked' : '' }}>
            <label for="is_active" class="mb-0">Active</label>
          </div>
          <div class="col-12"><button class="btn btn-gold mt-2" type="submit">{{ $item->exists ? 'Save Changes' : 'Create' }}</button></div>
        </div>
      </form>
    </div>
  </div>
</div>

@endsection
