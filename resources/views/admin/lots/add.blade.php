@extends('admin.layouts.app')
@section('title', 'Add Lot')

@section('content')
    <div class="row mt-lg-4 pt-lg-4">
        <div class="page-inner">
            <div class="page-header d-flex align-items-center justify-content-between flex-wrap mb-3">
                <div class="d-flex align-items-center gap-3">
                    <h3 class="fw-bold mb-0">Add Lot</h3>
                    <ul class="breadcrumbs d-flex align-items-center mb-0">
                        <li class="nav-home me-2">
                            <a href="{{ route('admin.dashboard') }}">
                                <i class="icon-home"></i>
                            </a>
                        </li>
                        <li class="separator me-2">
                            <i class="icon-arrow-right"></i>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('admin.lots.index') }}">Lots</a>
                        </li>
                        <li class="separator"><i class="icon-arrow-right"></i></li>
                        <li class="nav-item"><a href="javscript:;">Add Lot</a></li>
                    </ul>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <div class="card-title">Lot Information</div>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('admin.lots.store') }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <div class="row">
                                    <!-- Column 1 -->
                                    <div class="col-md-6 col-lg-4">
                                        <div class="form-group">
                                            <label for="seller_id">Select Seller</label>
                                            <select name="seller_id" id="seller_id" class="form-control" required>
                                                <option value="">-- Select Seller --</option>
                                                @foreach ($sellers as $seller)
                                                    <option value="{{ $seller->id }}">{{ $seller->full_name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="form-group">
                                            <label for="category_id">Select Category</label>
                                            <select name="category_id" id="category_id" class="form-control" required>
                                                <option value="">-- Select Category --</option>
                                                @foreach ($categories as $category)
                                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="form-group">
                                            <label for="type">Type</label>
                                            <input type="text" name="type" id="type" class="form-control"
                                                placeholder="Enter Type" required>
                                        </div>

                                        <div class="form-group">
                                            <label for="color">Color</label>
                                            <input type="text" name="color" id="color" class="form-control"
                                                placeholder="Enter Color">
                                        </div>

                                        <div class="form-group">
                                            <label for="weight">Weight</label>
                                            <input type="text" name="weight" id="weight" class="form-control"
                                                placeholder="Enter Weight" required>
                                        </div>


                                        <div class="form-group">
                                            <label for="size">Size</label>
                                            <input type="text" name="size" id="size" class="form-control"
                                                placeholder="Enter Size">
                                        </div>

                                        <div class="form-group">
                                            <label for="status">Status</label>
                                            <select name="status" id="status" class="form-control">
                                                <option value="0">Pending</option>
                                                <option value="1">Active</option>
                                                <option value="2">Sold</option>
                                            </select>
                                        </div>

                                        <div class="form-group">
                                            <label>Upload Images</label>
                                            <div id="image-upload-group">
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
                                                placeholder="Enter Shape">
                                        </div>

                                        <div class="form-group">
                                            <label for="batch_code">Batch Code</label>
                                            <input type="text" name="batch_code" id="batch_code" class="form-control"
                                                placeholder="Enter Batch Code">
                                        </div>

                                        <div class="form-group">
                                            <label for="report_number">Report Number</label>
                                            <input type="text" name="report_number" id="report_number"
                                                class="form-control" placeholder="Enter Report Number">
                                        </div>

                                        <div class="form-group">
                                            <label for="colour_grade">Colour Grade</label>
                                            <input type="text" name="colour_grade" id="colour_grade"
                                                class="form-control" placeholder="Enter Colour Grade">
                                        </div>

                                        <div class="form-group">
                                            <label for="colour_origin">Colour Origin</label>
                                            <input type="text" name="colour_origin" id="colour_origin"
                                                class="form-control" placeholder="Enter Colour Origin">
                                        </div>

                                        <div class="form-group">
                                            <label for="colour_distribution">Colour Distribution</label>
                                            <input type="text" name="colour_distribution" id="colour_distribution"
                                                class="form-control" placeholder="Enter Colour Distribution">
                                        </div>
                                    </div>

                                    <!-- Column 3 -->
                                    <div class="col-md-6 col-lg-4">
                                        <div class="form-group">
                                            <label for="polish">Polish</label>
                                            <input type="text" name="polish" id="polish" class="form-control"
                                                placeholder="Enter Polish">
                                        </div>

                                        <div class="form-group">
                                            <label for="symmetry">Symmetry</label>
                                            <input type="text" name="symmetry" id="symmetry" class="form-control"
                                                placeholder="Enter Symmetry">
                                        </div>

                                        <div class="form-group">
                                            <label for="fluorescence">Fluorescence</label>
                                            <input type="text" name="fluorescence" id="fluorescence"
                                                class="form-control" placeholder="Enter Fluorescence">
                                        </div>

                                        <div class="form-group">
                                            <label for="stone">Stone</label>
                                            <input type="text" name="stone" id="stone" class="form-control"
                                                placeholder="Enter Stone">
                                        </div>

                                        <div class="form-group">
                                            <label for="notes">Notes</label>
                                            <textarea name="notes" id="notes" rows="4" class="form-control" placeholder="Enter any notes..."></textarea>
                                        </div>

                                    </div>
                                </div>

                                <div class="card-action mt-4">
                                    <button type="submit" class="btn btn-success">Save</button>
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
                img.classList.add('rounded', 'p-1');
                previewContainer.appendChild(img);
            };
            reader.readAsDataURL(file);
        }

        function handleFileInput(input) {
            const previewContainer = input.closest('.input-group').nextElementSibling;
            previewContainer.innerHTML = ''; // Clear previous previews
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
