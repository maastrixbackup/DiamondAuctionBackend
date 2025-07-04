@extends('admin.layouts.app')
@section('title', 'View Lot')

@section('content')
    <div class="row mt-lg-4 pt-lg-4">
        <div class="page-inner">
            <div class="page-header d-flex align-items-center justify-content-between flex-wrap mb-3">
                <div class="d-flex align-items-center gap-3">
                    <h3 class="fw-bold mb-0">Lots</h3>
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
                        <li class="nav-item">View Lot</li>
                    </ul>
                </div>
            </div>

            <div class="row g-4">
                <div class="col-lg-6">
                    <div class="card shadow-sm">
                        @if ($lot->images && is_array($lot->images) && count($lot->images))
                            <!-- Main image carousel -->
                            <div id="lotImageCarousel" class="carousel slide" data-bs-ride="carousel">
                                <div class="carousel-inner">
                                    @foreach ($lot->images as $index => $image)
                                        <div class="carousel-item {{ $index === 0 ? 'active' : '' }}">
                                            <img src="{{ asset('storage/images/lots/' . $image) }}"
                                                class="d-block w-100 img-fluid rounded-top"
                                                style="object-fit: cover; max-height: 400px;">
                                        </div>
                                    @endforeach
                                </div>
                                <!-- Carousel controls -->
                                @if (count($lot->images) > 1)
                                    <button class="carousel-control-prev" type="button" data-bs-target="#lotImageCarousel"
                                        data-bs-slide="prev">
                                        <span class="carousel-control-prev-icon"></span>
                                    </button>
                                    <button class="carousel-control-next" type="button" data-bs-target="#lotImageCarousel"
                                        data-bs-slide="next">
                                        <span class="carousel-control-next-icon"></span>
                                    </button>
                                @endif
                            </div>
                        @else
                            <div class="p-5 text-center text-muted">No image available</div>
                        @endif

                        <div
                            class="card-body border-top d-flex flex-wrap justify-content-around text-secondary small fw-semibold">
                            <div><i class="fa fa-ruler-combined me-1 text-primary"></i>Size: {{ $lot->size ?? '-' }}</div>
                            <div><i class="fa fa-gem me-1 text-primary"></i>Stones: {{ $lot->stone ?? '-' }}</div>
                            <div><i class="fa fa-weight-hanging me-1 text-primary"></i>Weight: {{ $lot->weight ?? '-' }}
                            </div>
                            <div><i class="fa fa-shapes me-1 text-primary"></i>Shape: {{ $lot->shape ?? '-' }}</div>
                        </div>
                    </div>
                    {{-- Video Preview Section --}}
                    @if (!empty($lot->video))
                        <div class="card mt-3 shadow-sm">
                            <div class="card-body">
                                <label for="video" class="form-label fw-semibold">Video</label>
                                <input type="hidden" name="video" id="video" class="form-control"
                                    placeholder="Enter Video link" value="{{ $lot->video }}"
                                    onkeyup="generatePreview(this.value)" />
                                <div class="my-3" id="previewContainer"></div>
                            </div>
                        </div>
                    @else
                        <div class="card mt-3 shadow-sm">
                            <div class="card-body text-muted text-center">
                                No video available
                            </div>
                        </div>
                    @endif

                </div>

                <!-- Right: Lot Info -->
                <div class="col-lg-6">
                    <div class="card mb-4 shadow-sm border-0">
                        <div class="card-body">
                            <h5 class="card-title border-bottom pb-2 mb-3 text-primary">
                                <i class="fas fa-gem me-2"></i>Lot Details
                            </h5>

                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="bg-light rounded-3 p-3">
                                        <div><strong>Lot ID:</strong> {{ $lot->id }}</div>
                                        <div><strong>Seller:</strong> {{ $lot->seller?->full_name ?? 'N/A' }}</div>
                                        <div><strong>Category:</strong> {{ $lot->category?->name ?? 'N/A' }}</div>
                                        <div><strong>Type:</strong> {{ $lot->type }}</div>
                                        <div><strong>Color:</strong> {{ $lot->color }}</div>
                                        <div><strong>Status:</strong>
                                            @if ($lot->status == 0)
                                                <span class="badge bg-danger">Pending</span>
                                            @elseif ($lot->status == 1)
                                                <span class="badge bg-success">Live</span>
                                            @else
                                                <span class="badge bg-secondary">Sold</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="bg-light rounded-3 p-3">
                                        <div><strong>Weight:</strong> {{ $lot->weight }}</div>
                                        <div><strong>Size:</strong> {{ $lot->size }}</div>
                                        <div><strong>Stone:</strong> {{ $lot->stone }}</div>
                                        <div><strong>Shape:</strong> {{ $lot->shape }}</div>
                                        <div><strong>Clarity:</strong> {{ $lot->clarity }}</div>
                                        <div><strong>Batch Code:</strong> {{ $lot->batch_code }}</div>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-4">
                                <strong>Notes:</strong>
                                <div class="bg-light rounded-3 p-3 mt-1">
                                    {!! nl2br(e($lot->notes)) !!}
                                </div>
                            </div>
                            <div class="mt-1">
                                <strong>Description:</strong>
                                <div class="bg-light rounded-3 p-3 mt-1">
                                    {{-- {!! nl2br(e($lot->description)) !!} --}}
                                    {!! $lot->description ? nl2br(e($lot->description)) : 'N/A' !!}
                                </div>
                            </div>
                        </div>
                    </div>


                    <div class="card shadow-sm border-0">
                        <div class="card-body">
                            <h5 class="card-title border-bottom pb-2 mb-3 text-primary">
                                <i class="fas fa-file-alt me-2"></i>Report & Quality Details
                            </h5>

                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="bg-light rounded-3 p-3">
                                        <div><strong>Report Number:</strong> {{ $lot->report_number }}</div>
                                        <div><strong>Colour Grade:</strong> {{ $lot->colour_grade }}</div>
                                        <div><strong>Origin:</strong> {{ $lot->colour_origin }}</div>
                                        <div><strong>Distribution:</strong> {{ $lot->colour_distribution }}</div>
                                        <div>
                                            <strong>Report Document:</strong>
                                            @if ($lot->report_document)
                                                <a href="{{ asset('storage/document/lots/' . $lot->report_document) }}"
                                                    target="_blank" class="btn btn-sm btn-primary">View</a>
                                            @else
                                                N/A
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="bg-light rounded-3 p-3">
                                        <div><strong>Polish:</strong> {{ $lot->polish }}</div>
                                        <div><strong>Symmetry:</strong> {{ $lot->symmetry }}</div>
                                        <div><strong>Fluorescence:</strong> {{ $lot->fluorescence }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

                <div class="col-12">
                    <a href="{{ route('admin.lots.index') }}" class="btn btn-secondary mt-4">Back</a>
                </div>
            </div>

        </div>
    </div>
@endsection

@push('scripts')
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
                     style="width: 475px; cursor: pointer;"
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
                             style="width: 475px; cursor: pointer;"
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
            <div class="ratio ratio-16x9">
                <iframe src="https://www.youtube.com/embed/${videoId}?autoplay=1"
                        allow="autoplay; encrypted-media"
                        allowfullscreen></iframe>
            </div>
        `;
            } else if (platform === 'vimeo') {
                container.innerHTML = `
            <div class="ratio ratio-16x9">
                <iframe src="https://player.vimeo.com/video/${videoId}?autoplay=1"
                        allow="autoplay; fullscreen"
                        allowfullscreen></iframe>
            </div>
        `;
            }
        }
    </script>
@endpush
