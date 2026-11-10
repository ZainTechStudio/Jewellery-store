@extends('layouts.admin')

@section('title', $product->exists ? 'Edit Product' : 'New Product')

@section('content')

<div class="row g-4">
  <div class="col-lg-8">
    <div class="admin-card admin-form">
      <h2 class="h6 text-gold mb-3">{{ $product->exists ? 'Edit Product' : 'New Product' }}</h2>
      <form method="POST" action="{{ $product->exists ? route('admin.products.update', $product) : route('admin.products.store') }}">
        @csrf
        @if($product->exists) @method('PATCH') @endif

        <div class="row g-3">
          <div class="col-md-6"><label>Name</label><input type="text" name="name" value="{{ old('name', $product->name) }}" required class="form-control"></div>
          <div class="col-md-6"><label>Slug</label><input type="text" name="slug" value="{{ old('slug', $product->slug) }}" required class="form-control"></div>
          <div class="col-md-6"><label>SKU</label><input type="text" name="sku" value="{{ old('sku', $product->sku) }}" required class="form-control"></div>
          <div class="col-md-6"><label>Category</label>
            <select name="category_id" required class="form-select">
              <option value="">Select category</option>
              @foreach($categories as $category)
                <option value="{{ $category->id }}" {{ (string) old('category_id', $product->category_id) === (string) $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
              @endforeach
            </select>
          </div>

          <div class="col-md-4"><label>Price</label><input type="number" step="0.01" min="0" name="price" value="{{ old('price', $product->price) }}" required class="form-control"></div>
          <div class="col-md-4"><label>Sale Price</label><input type="number" step="0.01" min="0" name="sale_price" value="{{ old('sale_price', $product->sale_price) }}" class="form-control"></div>
          <div class="col-md-4"><label>Stock Quantity</label><input type="number" min="0" name="stock_quantity" value="{{ old('stock_quantity', $product->stock_quantity) }}" required class="form-control"></div>

          <div class="col-md-3"><label>Gender</label>
            <select name="gender" class="form-select">
              <option value="">—</option>
              @foreach(['women','men','kids','unisex'] as $g)
                <option value="{{ $g }}" {{ old('gender', $product->gender) === $g ? 'selected' : '' }}>{{ ucfirst($g) }}</option>
              @endforeach
            </select>
          </div>
          <div class="col-md-3"><label>Metal Type</label>
            <select name="metal_type_id" class="form-select">
              <option value="">—</option>
              @foreach($metalTypes as $mt)
                <option value="{{ $mt->id }}" {{ (string) old('metal_type_id', $product->metal_type_id) === (string) $mt->id ? 'selected' : '' }}>{{ $mt->name }}</option>
              @endforeach
            </select>
          </div>
          <div class="col-md-3"><label>Gemstone</label>
            <select name="gemstone_id" class="form-select">
              <option value="">—</option>
              @foreach($gemstones as $g)
                <option value="{{ $g->id }}" {{ (string) old('gemstone_id', $product->gemstone_id) === (string) $g->id ? 'selected' : '' }}>{{ $g->name }}</option>
              @endforeach
            </select>
          </div>
          <div class="col-md-3"><label>Occasion</label>
            <select name="occasion_id" class="form-select">
              <option value="">—</option>
              @foreach($occasions as $o)
                <option value="{{ $o->id }}" {{ (string) old('occasion_id', $product->occasion_id) === (string) $o->id ? 'selected' : '' }}>{{ $o->name }}</option>
              @endforeach
            </select>
          </div>

          <div class="col-md-6"><label>Metal Purity</label><input type="text" name="metal_purity" value="{{ old('metal_purity', $product->metal_purity) }}" class="form-control" placeholder="e.g. 18k"></div>
          <div class="col-md-6"><label>Sort Order</label><input type="number" min="0" name="sort_order" value="{{ old('sort_order', $product->sort_order ?? 0) }}" class="form-control"></div>

          <div class="col-12"><label>Short Description</label><input type="text" name="short_description" value="{{ old('short_description', $product->short_description) }}" class="form-control"></div>
          <div class="col-12"><label>Description</label><textarea name="description" rows="4" class="form-control">{{ old('description', $product->description) }}</textarea></div>

          <div class="col-6 d-flex align-items-center gap-2">
            <input type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', $product->exists ? $product->is_active : true) ? 'checked' : '' }}>
            <label for="is_active" class="mb-0">Active</label>
          </div>
          <div class="col-6 d-flex align-items-center gap-2">
            <input type="checkbox" id="is_featured" name="is_featured" value="1" {{ old('is_featured', $product->is_featured) ? 'checked' : '' }}>
            <label for="is_featured" class="mb-0">Featured</label>
          </div>

          <div class="col-12"><button class="btn btn-gold mt-2" type="submit">{{ $product->exists ? 'Save Changes' : 'Create Product' }}</button></div>
        </div>
      </form>
    </div>
  </div>

  <div class="col-lg-4">
    @if($product->exists)
      <div class="admin-card admin-form mb-4">
        <h2 class="h6 text-gold mb-3">Images</h2>
        @foreach($product->images as $image)
          <div class="d-flex align-items-center gap-2 mb-2">
            <img src="{{ asset($image->image_path) }}" class="admin-thumb">
            <div class="flex-grow-1" style="font-size:.78rem">
              {{ $image->is_primary ? 'Primary' : 'Image' }}
              @unless($image->is_primary)
                <form method="POST" action="{{ route('admin.product-images.primary', $image) }}" class="d-inline">
                  @csrf @method('PATCH')
                  <button type="submit" class="text-gold border-0 bg-transparent p-0" style="font-size:.76rem">Make primary</button>
                </form>
              @endunless
            </div>
            <form method="POST" action="{{ route('admin.product-images.destroy', $image) }}" onsubmit="return confirm('Remove this image?');">
              @csrf @method('DELETE')
              <button type="submit" class="text-muted-lux border-0 bg-transparent p-0"><i class="bi bi-trash"></i></button>
            </form>
          </div>
        @endforeach
        <form method="POST" action="{{ route('admin.products.images.store', $product) }}" enctype="multipart/form-data" class="mt-3">
          @csrf
          <input type="file" name="image" accept="image/jpeg,image/png,image/webp" required class="form-control form-control-sm mb-2">
          <input type="text" name="alt_text" placeholder="Alt text (optional)" class="form-control form-control-sm mb-2">
          <button class="btn btn-ghost btn-sm w-100" type="submit">Upload Image</button>
        </form>
      </div>

      <div class="admin-card admin-form">
        <h2 class="h6 text-gold mb-3">Variants</h2>
        @foreach($product->variants as $variant)
          <div class="d-flex justify-content-between align-items-center mb-2" style="font-size:.8rem;border-bottom:1px solid rgba(255,255,255,.06);padding-bottom:.5rem">
            <div>
              <strong>{{ $variant->name }}</strong><br>
              <span class="text-muted-lux">Stock: {{ $variant->stock_quantity }} {{ $variant->price !== null ? '· $'.number_format((float) $variant->price) : '' }}</span>
            </div>
            <form method="POST" action="{{ route('admin.product-variants.destroy', $variant) }}" onsubmit="return confirm('Remove this variant?');">
              @csrf @method('DELETE')
              <button type="submit" class="text-muted-lux border-0 bg-transparent p-0"><i class="bi bi-trash"></i></button>
            </form>
          </div>
        @endforeach
        <form method="POST" action="{{ route('admin.products.variants.store', $product) }}" class="mt-3">
          @csrf
          <div class="row g-2">
            <div class="col-6"><input type="text" name="option_key" placeholder="e.g. ring_size" required class="form-control form-control-sm"></div>
            <div class="col-6"><input type="text" name="option_value" placeholder="e.g. 7" required class="form-control form-control-sm"></div>
            <div class="col-6"><input type="number" step="0.01" min="0" name="price" placeholder="Price (optional)" class="form-control form-control-sm"></div>
            <div class="col-6"><input type="number" min="0" name="stock_quantity" placeholder="Stock" required value="0" class="form-control form-control-sm"></div>
            <div class="col-12"><button class="btn btn-ghost btn-sm w-100" type="submit">Add Variant</button></div>
          </div>
        </form>
      </div>
    @else
      <div class="admin-card">
        <p class="text-muted-lux mb-0" style="font-size:.85rem">Save the product first to add images and variants.</p>
      </div>
    @endif
  </div>
</div>

@endsection
