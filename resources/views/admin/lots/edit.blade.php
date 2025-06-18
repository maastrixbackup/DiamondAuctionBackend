@extends('admin.layouts.app')
@section('title', 'Edit Lot')

@section('content')
    <div class="row mt-lg-4 pt-lg-4">
        <div class="page-inner">
            <div class="page-header d-flex align-items-center justify-content-between flex-wrap mb-3">
                <div class="d-flex align-items-center gap-3">
                    <h3 class="fw-bold mb-0">Edit Lot</h3>
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
                        <li class="nav-item"><a href="#">Edit Lot</a></li>
                    </ul>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <div class="card shadow-sm border-0 rounded-4">
                        <div class="card-header bg-light py-3 px-4 border-bottom">
                            <h5 class="card-title mb-0 fw-semibold">Update Lot Information</h5>
                        </div>
                        <div class="card-body px-4 py-4">
                            <form action="{{ route('admin.lots.update', $lot->id) }}" method="POST"
                                enctype="multipart/form-data">
                                @csrf
                                @method('PUT')
                                <div class="row gy-4">
                                    <!-- Column 1 -->
                                    <div class="col-md-6 col-lg-4">
                                        <div class="mb-3">
                                            <label for="seller_id" class="form-label">Select Seller</label>
                                            <select name="seller_id" id="seller_id" class="form-select" required>
                                                <option value="">-- Select Seller --</option>
                                                @foreach ($sellers as $seller)
                                                    <option value="{{ $seller->id }}"
                                                        {{ $lot->seller_id == $seller->id ? 'selected' : '' }}>
                                                        {{ $seller->full_name }} ({{ $seller->id }})
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="mb-3">
                                            <label for="category_id" class="form-label">Select Category</label>
                                            <select name="category_id" id="category_id" class="form-select" required>
                                                <option value="">-- Select Category --</option>
                                                @foreach ($categories as $category)
                                                    <option value="{{ $category->id }}"
                                                        {{ $lot->category_id == $category->id ? 'selected' : '' }}>
                                                        {{ $category->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="mb-3">
                                            <label for="title" class="form-label">Title</label>
                                            <input type="text" name="title" id="title" class="form-control"
                                                placeholder="Enter Title" value="{{ $lot->title }}" required>
                                        </div>

                                        <div class="mb-3">
                                            <label for="type" class="form-label">Type</label>
                                            <input type="text" name="type" id="type" class="form-control"
                                                placeholder="Enter Type" value="{{ $lot->type }}" required>
                                        </div>

                                        <div class="mb-3">
                                            <label for="color" class="form-label">Color</label>
                                            <input type="text" name="color" id="color" class="form-control"
                                                placeholder="Enter Color" value="{{ $lot->color }}">
                                        </div>

                                        <div class="mb-3">
                                            <label for="weight" class="form-label">Weight</label>
                                            <input type="text" name="weight" id="weight" class="form-control"
                                                placeholder="Enter Weight" value="{{ $lot->weight }}" required>
                                        </div>

                                        <div class="mb-3">
                                            <label for="size" class="form-label">Size</label>
                                            <input type="text" name="size" id="size" class="form-control"
                                                placeholder="Enter Size" value="{{ $lot->size }}">
                                        </div>

                                        {{-- <div class="mb-3">
                                            <label for="status" class="form-label">Status</label>
                                            <select name="status" id="status" class="form-select">
                                                <option value="0" {{ $lot->status == 0 ? 'selected' : '' }}>Pending
                                                </option>
                                                <option value="1" {{ $lot->status == 1 ? 'selected' : '' }}>Live
                                                </option>
                                                <option value="2" {{ $lot->status == 2 ? 'selected' : '' }}>Sold
                                                </option>
                                            </select>
                                        </div> --}}

                                        <div class="mb-3">
                                            <label class="form-label">Upload Images</label>
                                            <div id="image-upload-group">
                                                {{-- Existing images --}}
                                                @if ($lot->images && is_array($lot->images))
                                                    @foreach ($lot->images as $img)
                                                        <div class="input-group mb-2">
                                                            <input type="file" name="images[]"
                                                                class="form-control image-input" accept="image/*">
                                                            <button type="button"
                                                                class="btn btn-outline-danger btn-sm remove-image">-</button>
                                                        </div>
                                                        <div class="preview-group d-flex flex-wrap gap-2 mb-3">
                                                            <img src="{{ asset('storage/images/lots/' . $img) }}"
                                                                class="rounded border p-1"
                                                                style="max-width: 100px; max-height: 100px;">
                                                            <input type="hidden" name="existing_images[]"
                                                                value="{{ $img }}">
                                                        </div>
                                                    @endforeach
                                                @endif

                                                {{-- Add new image input --}}
                                                <div class="input-group mb-2">
                                                    <input type="file" name="images[]"
                                                        class="form-control image-input" accept="image/*">
                                                    <button type="button"
                                                        class="btn btn-outline-success btn-sm add-more-image">+</button>
                                                </div>
                                                <div class="preview-group d-flex flex-wrap gap-2"></div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Column 2 -->
                                    <div class="col-md-6 col-lg-4">
                                        <div class="mb-3">
                                            <label for="shape" class="form-label">Shape</label>
                                            <input type="text" name="shape" id="shape" class="form-control"
                                                placeholder="Enter Shape" value="{{ $lot->shape }}">
                                        </div>

                                        <div class="mb-3">
                                            <label for="batch_code" class="form-label">Batch Code</label>
                                            <input type="text" name="batch_code" id="batch_code" class="form-control"
                                                placeholder="Enter Batch Code" value="{{ $lot->batch_code }}">
                                        </div>

                                        <div class="mb-3">
                                            <label for="report_number" class="form-label">Report Number</label>
                                            <input type="text" name="report_number" id="report_number"
                                                class="form-control" placeholder="Enter Report Number"
                                                value="{{ $lot->report_number }}">
                                        </div>

                                        <div class="mb-3">
                                            <label for="colour_grade" class="form-label">Colour Grade</label>
                                            <input type="text" name="colour_grade" id="colour_grade"
                                                class="form-control" placeholder="Enter Colour Grade"
                                                value="{{ $lot->colour_grade }}">
                                        </div>

                                        <div class="mb-3">
                                            <label for="colour_origin" class="form-label">Origin</label>
                                            <input type="text" name="colour_origin" id="colour_origin"
                                                class="form-control" placeholder="Enter Origin"
                                                value="{{ $lot->colour_origin }}">
                                        </div>

                                        <div class="mb-3">
                                            <label for="colour_distribution" class="form-label">Distribution</label>
                                            <input type="text" name="colour_distribution" id="colour_distribution"
                                                class="form-control" placeholder="Enter Distribution"
                                                value="{{ $lot->colour_distribution }}">
                                        </div>

                                        <div class="mb-3">
                                            <label for="status" class="form-label">Status</label>
                                            <select name="status" id="status" class="form-select">
                                                <option value="0" {{ $lot->status == 0 ? 'selected' : '' }}>Pending
                                                </option>
                                                <option value="1" {{ $lot->status == 1 ? 'selected' : '' }}>Live
                                                </option>
                                                <option value="2" {{ $lot->status == 2 ? 'selected' : '' }}>Sold
                                                </option>
                                            </select>
                                        </div>

                                        <div class="mb-3">
                                            <label for="video" class="form-label">Video Link</label>
                                            <input type="text" name="video" id="video" class="form-control"
                                                placeholder="Enter Video link" value="{{ $lot->video }}"
                                                onkeyup="generatePreview(this.value)">
                                            <div class="my-2" id="previewContainer"></div>
                                        </div>
                                    </div>

                                    <!-- Column 3 -->
                                    <div class="col-md-6 col-lg-4">
                                        <div class="mb-3">
                                            <label for="polish" class="form-label">Polish</label>
                                            <input type="text" name="polish" id="polish" class="form-control"
                                                placeholder="Enter Polish" value="{{ $lot->polish }}">
                                        </div>

                                        <div class="mb-3">
                                            <label for="symmetry" class="form-label">Symmetry</label>
                                            <input type="text" name="symmetry" id="symmetry" class="form-control"
                                                placeholder="Enter Symmetry" value="{{ $lot->symmetry }}">
                                        </div>

                                        <div class="mb-3">
                                            <label for="report_document" class="form-label">Report Document(PDF)</label>
                                            <input type="file" name="report_document" id="report_document"
                                                class="form-control" accept="application/pdf" />
                                            @if ($lot->report_document)
                                                <div class="mt-2">
                                                    <a href="{{ asset('storage/document/lots/' . $lot->report_document) }}"
                                                        target="_blank">
                                                        {{ $lot->report_document }}
                                                    </a>
                                                </div>
                                            @endif
                                        </div>

                                        <div class="mb-3">
                                            <label for="fluorescence" class="form-label">Fluorescence</label>
                                            <input type="text" name="fluorescence" id="fluorescence"
                                                class="form-control" placeholder="Enter Fluorescence"
                                                value="{{ $lot->fluorescence }}">
                                        </div>

                                        <div class="mb-3">
                                            <label for="stone" class="form-label">Stone</label>
                                            <input type="text" name="stone" id="stone" class="form-control"
                                                placeholder="Enter Stone" value="{{ $lot->stone }}">
                                        </div>

                                        <div class="mb-3">
                                            <label for="notes" class="form-label">Notes</label>
                                            <textarea name="notes" id="notes" rows="4" class="form-control" placeholder="Enter any notes...">{{ $lot->notes }}</textarea>
                                        </div>

                                        <div class="mb-3">
                                            <label for="description" class="form-label">Description</label>
                                            <textarea name="description" id="description" rows="4" class="form-control"
                                                placeholder="Enter description...">{{ $lot->description }}</textarea>
                                        </div>
                                    </div>
                                </div>

                                <div class="mt-4 d-flex gap-2">
                                    <button type="submit" class="btn btn-success px-4">Update</button>
                                    <a href="{{ route('admin.lots.index') }}"
                                        class="btn btn-outline-danger px-4">Cancel</a>
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
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const urlField = document.getElementById("video");
            if (urlField.value.trim()) {
                generatePreview(urlField.value.trim());
            }
        });

        function generatePreview(url) {
            const previewContainer = document.getElementById('previewContainer');
            previewContainer.innerHTML = '';

            const youtubeId = extractYouTubeId(url);
            const vimeoId = extractVimeoId(url);

            if (youtubeId) {
                previewContainer.innerHTML = `
                <img src="https://img.youtube.com/vi/${youtubeId}/hqdefault.jpg"
                     alt="YouTube Thumbnail"
                     style="width: 100px; cursor: pointer;"
                     onclick="playVideo('${youtubeId}', 'youtube')">
            `;
            } else if (vimeoId) {
                // Vimeo thumbnail via API
                fetch(`https://vimeo.com/api/v2/video/${vimeoId}.json`)
                    .then(response => response.json())
                    .then(data => {
                        const thumbnail = data[0]?.thumbnail_small || '';
                        previewContainer.innerHTML = `
                        <img src="${thumbnail}"
                             alt="Vimeo Thumbnail"
                             style="width: 100px; cursor: pointer;"
                             onclick="playVideo('${vimeoId}', 'vimeo')">
                    `;
                    }).catch(err => {
                        console.error("Vimeo fetch failed", err);
                        previewContainer.innerHTML = `<p style="color:red;">Unable to load Vimeo preview</p>`;
                    });
            } else if (url.trim()) {
                previewContainer.innerHTML = `<p style="color: red;">Invalid or unsupported video URL</p>`;
            }
        }

        function extractYouTubeId(url) {
            const match = url.match(
                /(?:https?:\/\/)?(?:www\.)?(?:youtube\.com\/(?:watch\?v=|embed\/|v\/)|youtu\.be\/)([^"&?\/\s]{11})/);
            return match ? match[1] : null;
        }

        function extractVimeoId(url) {
            const match = url.match(/(?:https?:\/\/)?(?:www\.)?vimeo\.com\/(?:.*\/)?(\d+)/);
            return match ? match[1] : null;
        }

        function playVideo(videoId, platform) {
            const container = document.getElementById("previewContainer");

            if (platform === 'youtube') {
                container.innerHTML = `
                <iframe width="315" height="215" src="https://www.youtube.com/embed/${videoId}?autoplay=1" frameborder="0" allow="autoplay; encrypted-media" allowfullscreen></iframe>
            `;
            } else if (platform === 'vimeo') {
                container.innerHTML = `
                <iframe width="315" height="215" src="https://player.vimeo.com/video/${videoId}?autoplay=1" frameborder="0" allow="autoplay; fullscreen" allowfullscreen></iframe>
            `;
            }
        }
    </script>
@endpush
