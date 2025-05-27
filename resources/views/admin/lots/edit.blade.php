@extends('admin.layouts.app')
@section('title', 'Edit Lot')

@section('content')
    <div class="row mt-lg-4 pt-lg-4">
        <div class="page-inner">
            <div class="page-header">
                <h3 class="fw-bold mb-3">Edit Lot</h3>
                <ul class="breadcrumbs mb-3">
                    <li class="nav-home">
                        <a href="{{ route('admin.dashboard') }}">
                            <i class="icon-home"></i>
                        </a>
                    </li>
                    <li class="separator"><i class="icon-arrow-right"></i></li>
                    <li class="nav-item"><a href="{{ route('admin.lots.index') }}">Lots</a></li>
                    <li class="separator"><i class="icon-arrow-right"></i></li>
                    <li class="nav-item"><a href="#">Edit Lot</a></li>
                </ul>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <div class="card-title">Update Lot Information</div>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('admin.lots.update', $lot->id) }}" method="POST"
                                enctype="multipart/form-data">
                                @csrf
                                @method('PUT')
                                <div class="row">
                                    <!-- Column 1 -->
                                    <div class="col-md-6 col-lg-4">
                                        <div class="form-group">
                                            <label for="seller_id">Select Seller</label>
                                            <select name="seller_id" id="seller_id" class="form-control" required>
                                                <option value="">-- Select Seller --</option>
                                                @foreach ($sellers as $seller)
                                                    <option value="{{ $seller->id }}"
                                                        {{ $lot->seller_id == $seller->id ? 'selected' : '' }}>
                                                        {{ $seller->full_name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="form-group">
                                            <label for="category_id">Select Category</label>
                                            <select name="category_id" id="category_id" class="form-control" required>
                                                <option value="">-- Select Category --</option>
                                                @foreach ($categories as $category)
                                                    <option value="{{ $category->id }}"
                                                        {{ $lot->category_id == $category->id ? 'selected' : '' }}>
                                                        {{ $category->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="form-group">
                                            <label for="type">Type</label>
                                            <input type="text" name="type" id="type" class="form-control"
                                                placeholder="Enter Type" value="{{ $lot->type }}" required>
                                        </div>

                                        <div class="form-group">
                                            <label for="color">Color</label>
                                            <input type="text" name="color" id="color" class="form-control"
                                                placeholder="Enter Color" value="{{ $lot->color }}">
                                        </div>

                                        <div class="form-group">
                                            <label for="weight">Weight</label>
                                            <input type="text" name="weight" id="weight" class="form-control"
                                                placeholder="Enter Weight" value="{{ $lot->weight }}" required>
                                        </div>

                                        <div class="form-group">
                                            <label for="size">Size</label>
                                            <input type="text" name="size" id="size" class="form-control"
                                                placeholder="Enter Size" value="{{ $lot->size }}">
                                        </div>

                                        <div class="form-group">
                                            <label for="status">Status</label>
                                            <select name="status" id="status" class="form-control">
                                                <option value="0" {{ $lot->status == 0 ? 'selected' : '' }}>Pending
                                                </option>
                                                <option value="1" {{ $lot->status == 1 ? 'selected' : '' }}>Active
                                                </option>
                                                <option value="2" {{ $lot->status == 2 ? 'selected' : '' }}>Sold
                                                </option>
                                            </select>
                                        </div>

                                        <div class="form-group">
                                            <label>Upload Images</label>
                                            <div id="image-upload-group">
                                                {{-- Existing images --}}
                                                @if ($lot->images && is_array($lot->images))
                                                    @foreach ($lot->images as $img)
                                                        <div class="input-group mb-2">
                                                            <input type="file" name="images[]"
                                                                class="form-control image-input" accept="image/*">
                                                            <button type="button"
                                                                class="btn btn-sm btn-danger remove-image">-</button>
                                                        </div>
                                                        <div class="preview-group mb-2 d-flex flex-wrap gap-2">
                                                            <img src="{{ asset('storage/images/lots/' . $img) }}"
                                                                style="max-width: 100px; max-height: 100px;"
                                                                class="rounded border p-1">
                                                            <input type="hidden" name="existing_images[]"
                                                                value="{{ $img }}">
                                                        </div>
                                                    @endforeach
                                                @endif

                                                {{-- Add new image input --}}
                                                <div class="input-group mb-2">
                                                    <input type="file" name="images[]" class="form-control image-input"
                                                        accept="image/*">
                                                    <button type="button"
                                                        class="btn btn-sm btn-success add-more-image">+</button>
                                                </div>
                                                <div class="preview-group mb-2 d-flex flex-wrap gap-2"></div>
                                            </div>
                                        </div>



                                    </div>

                                    <!-- Column 2 -->
                                    <div class="col-md-6 col-lg-4">
                                        <div class="form-group">
                                            <label for="shape">Shape</label>
                                            <input type="text" name="shape" id="shape" class="form-control"
                                                placeholder="Enter Shape" value="{{ $lot->shape }}">
                                        </div>

                                        <div class="form-group">
                                            <label for="batch_code">Batch Code</label>
                                            <input type="text" name="batch_code" id="batch_code" class="form-control"
                                                placeholder="Enter Batch Code" value="{{ $lot->batch_code }}">
                                        </div>

                                        <div class="form-group">
                                            <label for="report_number">Report Number</label>
                                            <input type="text" name="report_number" id="report_number"
                                                class="form-control" placeholder="Enter Report Number"
                                                value="{{ $lot->report_number }}">
                                        </div>

                                        <div class="form-group">
                                            <label for="colour_grade">Colour Grade</label>
                                            <input type="text" name="colour_grade" id="colour_grade"
                                                class="form-control" placeholder="Enter Colour Grade"
                                                value="{{ $lot->colour_grade }}">
                                        </div>

                                        <div class="form-group">
                                            <label for="colour_origin">Colour Origin</label>
                                            <input type="text" name="colour_origin" id="colour_origin"
                                                class="form-control" placeholder="Enter Colour Origin"
                                                value="{{ $lot->colour_origin }}">
                                        </div>

                                        <div class="form-group">
                                            <label for="colour_distribution">Colour Distribution</label>
                                            <input type="text" name="colour_distribution" id="colour_distribution"
                                                class="form-control" placeholder="Enter Colour Distribution"
                                                value="{{ $lot->colour_distribution }}">
                                        </div>
                                    </div>

                                    <!-- Column 3 -->
                                    <div class="col-md-6 col-lg-4">
                                        <div class="form-group">
                                            <label for="polish">Polish</label>
                                            <input type="text" name="polish" id="polish" class="form-control"
                                                placeholder="Enter Polish" value="{{ $lot->polish }}">
                                        </div>

                                        <div class="form-group">
                                            <label for="symmetry">Symmetry</label>
                                            <input type="text" name="symmetry" id="symmetry" class="form-control"
                                                placeholder="Enter Symmetry" value="{{ $lot->symmetry }}">
                                        </div>

                                        <div class="form-group">
                                            <label for="fluorescence">Fluorescence</label>
                                            <input type="text" name="fluorescence" id="fluorescence"
                                                class="form-control" placeholder="Enter Fluorescence"
                                                value="{{ $lot->fluorescence }}">
                                        </div>

                                        <div class="form-group">
                                            <label for="stone">Stone</label>
                                            <input type="text" name="stone" id="stone" class="form-control"
                                                placeholder="Enter Stone" value="{{ $lot->stone }}">
                                        </div>

                                        <div class="form-group">
                                            <label for="notes">Notes</label>
                                            <textarea name="notes" id="notes" rows="4" class="form-control" placeholder="Enter any notes...">{{ $lot->notes }}</textarea>
                                        </div>
                                    </div>
                                </div>

                                <div class="card-action mt-4">
                                    <button type="submit" class="btn btn-success">Update</button>
                                    <a href="{{ route('admin.lots.index') }}" class="btn btn-danger">Cancel</a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function createImagePreview(file, previewContainer) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const img = document.createElement('img');
                img.src = e.target.result;
                img.style.maxWidth = '100px';
                img.style.maxHeight = '100px';
                img.classList.add('rounded', 'border', 'p-1');
                previewContainer.innerHTML = '';
                previewContainer.appendChild(img);
            };
            reader.readAsDataURL(file);
        }

        function handleFileInput(input) {
            const previewContainer = input.closest('.input-group').nextElementSibling;
            previewContainer.innerHTML = '';
            Array.from(input.files).forEach(file => {
                if (file.type.startsWith('image/')) {
                    createImagePreview(file, previewContainer);
                }
            });
        }

        document.addEventListener("DOMContentLoaded", function() {
            const uploadGroup = document.getElementById('image-upload-group');

            uploadGroup.addEventListener('click', function(e) {
                if (e.target.classList.contains('add-more-image')) {
                    const inputGroup = document.createElement('div');
                    inputGroup.classList.add('input-group', 'mb-2');
                    inputGroup.innerHTML = `
                    <input type="file" name="images[]" class="form-control image-input" accept="image/*">
                    <button type="button" class="btn btn-sm btn-danger remove-image">-</button>
                `;

                    const previewGroup = document.createElement('div');
                    previewGroup.classList.add('preview-group', 'mb-2', 'd-flex', 'flex-wrap', 'gap-2');

                    uploadGroup.appendChild(inputGroup);
                    uploadGroup.appendChild(previewGroup);
                }

                if (e.target.classList.contains('remove-image')) {
                    const inputGroup = e.target.closest('.input-group');
                    const previewGroup = inputGroup.nextElementSibling;
                    inputGroup.remove();
                    if (previewGroup && previewGroup.classList.contains('preview-group')) {
                        previewGroup.remove();
                    }
                }
            });

            uploadGroup.addEventListener('change', function(e) {
                if (e.target.classList.contains('image-input')) {
                    handleFileInput(e.target);
                }
            });
        });
    </script>
@endpush
