@php($prefix = $prefix ?? '')
<div class="mb-3">
	<label class="form-label">Product <span class="text-danger">*</span></label>
	<select name="iProductId" id="{{ $prefix }}iProductId" class="form-control">
		<option value="">Select Product</option>
		@foreach($products as $product)
		<option value="{{ $product->iProductId }}">{{ $product->strProductName }} ({{ $product->category->strCategoryName ?? 'No Category' }})</option>
	@endforeach
	</select>
	@error('iProductId')
	<span class="text-danger">{{ $message }}</span>@enderror	
</div>
<div class="mb-3">
	<label class="form-label">Showroom <span class="text-danger">*</span></label>
	<select name="iShowroomId" id="{{ $prefix }}iShowroomId" class="form-control">
		<option value="">Select Showroom</option>
		@foreach($showrooms as $showroom)
			<option value="{{ $showroom->iShowroomId }}">{{ $showroom->strShowRoomName }}</option>
		@endforeach
	</select>
	@error('iShowroomId')<span class="text-danger">{{ $message }}</span>@enderror
</div>
<div class="mb-3">
	<label class="form-label">Inside / Godown Stock <span class="text-danger">*</span></label>
	<input type="number" name="inside_quantity" id="{{ $prefix }}inside_quantity" class="form-control" min="0" value="{{ old('inside_quantity', 0) }}">
</div>
<div class="mb-3">
	<label class="form-label">Showroom Stock <span class="text-danger">*</span></label>
	<input type="number" name="showroom_quantity" id="{{ $prefix }}showroom_quantity" class="form-control" min="0" value="{{ old('showroom_quantity', 0) }}">
</div>
<div class="mb-3">
	<label class="form-label">Minimum Alert Qty</label><input type="number" name="minimum_quantity" id="{{ $prefix }}minimum_quantity" class="form-control" min="0" value="{{ old('minimum_quantity', 0) }}">
</div>
<div class="mb-3">
	<label class="form-label">Remarks</label>
	<textarea name="remarks" id="{{ $prefix }}remarks" class="form-control" rows="2">{{ old('remarks') }}</textarea>
</div>
