@extends('layouts.admin')

@section('title', $category->exists ? 'Edit Category' : 'New Category')

@section('content')

<div class="row justify-content-center">
  <div class="col-lg-7">
    <div class="admin-card admin-form">
      <h2 class="h6 text-gold mb-3">{{ $category->exists ? 'Edit Category' : 'New Category' }}</h2>
      <form method="POST" action="{{ $category->exists ? route('admin.categories.update', $category) : route('admin.categories.store') }}">
        @csrf
        @if($category->exists) @method('PATCH') @endif

        <div class="row g-3">
          <div class="col-md-6"><label>Name</label><input type="text" name="name" value="{{ old('name', $category->name) }}" required class="form-control"></div>
          <div class="col-md-6"><label>Slug</label><input type="text" name="slug" value="{{ old('slug', $category->slug) }}" required class="form-control"></div>
          <div class="col-md-6"><label>Parent Category</label>
            <select name="parent_id" class="form-select">
              <option value="">None (top-level)</option>
              @foreach($parents as $parent)
                <option value="{{ $parent->id }}" {{ (string) old('parent_id', $category->parent_id) === (string) $parent->id ? 'selected' : '' }}>{{ $parent->name }}</option>
              @endforeach
            </select>
          </div>
          <div class="col-md-6"><label>Sort Order</label><input type="number" min="0" name="sort_order" value="{{ old('sort_order', $category->sort_order ?? 0) }}" class="form-control"></div>
          <div class="col-12"><label>Description</label><textarea name="description" rows="3" class="form-control">{{ old('description', $category->description) }}</textarea></div>
          <div class="col-12"><label>Image path (optional)</label><input type="text" name="image" value="{{ old('image', $category->image) }}" class="form-control" placeholder="/img/p1.jpg"></div>
          <div class="col-12 d-flex align-items-center gap-2">
            <input type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', $category->exists ? $category->is_active : true) ? 'checked' : '' }}>
            <label for="is_active" class="mb-0">Active</label>
          </div>
          <div class="col-12"><button class="btn btn-gold mt-2" type="submit">{{ $category->exists ? 'Save Changes' : 'Create Category' }}</button></div>
        </div>
      </form>
    </div>
  </div>
</div>

@endsection
