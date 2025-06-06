@extends('admin.layouts.app')
@section('title', 'Slot Booking Requests')

@section('content')
    <div class="row mt-lg-4 pt-lg-4">
        <div class="page-inner">
            <div class="page-header d-flex align-items-center justify-content-between flex-wrap mb-3">
                <div class="d-flex align-items-center gap-3">
                    <h3 class="fw-bold mb-0">Manage Viewing Requests</h3>
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
                            <a href="#">Requests</a>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Viewing Requests</h4>
                </div>

                <div class="card-body px-4">
                    <div class="table-responsive">
                        <table id="viewingRequestTable" class="table table-striped table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>SL</th>
                                    <th>Bidder Name</th>
                                    <th>Room Name</th>
                                    <th>Room Type</th>
                                    <th>Date</th>
                                    <th>Start Time</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($groupedSlots as $index => $slot)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $slot->bidder_name }}</td>
                                        <td>{{ $slot->room_name ?? 'N/A' }}</td>
                                        <td>{{ $slot->room_type }}</td>
                                        <td>{{ $slot->date_for_reservation }}</td>
                                        <td>{{ \Carbon\Carbon::parse($slot->start_time)->format('h:i A') }}</td>
                                        <td>
                                            @if ($slot->status == 0)
                                                <span class="badge bg-warning">Pending</span>
                                            @elseif($slot->status == 1)
                                                <span class="badge bg-success">Approved</span>
                                            @else
                                                <span class="badge bg-danger">Rejected</span>
                                            @endif
                                        </td>
                                        <td>
                                            <button class="btn btn-sm btn-outline-primary d-flex align-items-center gap-1"
                                                onclick="viewLots(
                                                    '{{ $slot->bidder_id }}',
                                                    '{{ $slot->room_type }}',
                                                    '{{ $slot->start_time }}',
                                                    '{{ $slot->date_for_reservation }}'
                                                )">
                                                <i class="icon-eye"></i>
                                                <span>View</span>
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8">
                                            <div class="text-center py-3 text-muted">
                                                No pending slot booking requests found.
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <div class="modal fade" id="lotsModal" tabindex="-1" aria-labelledby="lotsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="lotsModalLabel">Lots for Slot</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="lotsModalBody">
                    {{-- Loading... --}}
                </div>
            </div>
        </div>
    </div>

@endsection
@push('scripts')
    <script>
        const viewLots = (bidderId, roomType, startTime, date) => {
            const params = new URLSearchParams({
                bidder_id: bidderId,
                room_type: roomType,
                start_time: startTime,
                date: date
            });

            const fullUrl = "{{ url('/admin/viewingRequestLots') }}" + '?' + params.toString();

            fetch(fullUrl)
                .then(response => response.text())
                .then(data => {
                    document.getElementById('lotsModalBody').innerHTML = data;
                    new bootstrap.Modal(document.getElementById('lotsModal')).show();
                })
                .catch(error => {
                    document.getElementById('lotsModalBody').innerHTML = 'Error loading lots.';
                    console.error(error);
                });
        };

        // function submitLotStatuses() {
        //     const form = document.getElementById('updateSlotForm');
        //     const formData = new FormData(form);

        //     fetch("{{ route('admin.updateLotsStatus') }}", {
        //             method: "POST",
        //             headers: {
        //                 'X-CSRF-TOKEN': "{{ csrf_token() }}"
        //             },
        //             body: formData
        //         })
        //         .then(response => response.json())
        //         .then(data => {
        //             if (data.success) {
        //                 const selects = form.querySelectorAll('select[name^="lot_status"]');
        //                 selects.forEach(select => {
        //                     const lotIdMatch = select.name.match(/lot_status\[(\d+)\]/);
        //                     if (!lotIdMatch) return;

        //                     const lotId = lotIdMatch[1];
        //                     const status = select.value;

        //                     const row = document.getElementById(`lot-row-${lotId}`);
        //                     if (!row) return;

        //                     const statusCell = row.children[6];
        //                     const actionCell = row.children[7];

        //                     if (status == 1) {
        //                         statusCell.innerHTML = '<span class="badge bg-success">Approved</span>';
        //                     } else if (status == 2) {
        //                         statusCell.innerHTML = '<span class="badge bg-danger">Rejected</span>';
        //                     }

        //                     actionCell.innerHTML = '<span class="text-muted"></span>';
        //                 });

        //                 const messageDiv = document.getElementById('successMessage');
        //                 messageDiv.textContent = "Lots updated successfully!";
        //                 messageDiv.classList.remove('d-none');

        //                 setTimeout(() => {
        //                     window.location.href = "{{ route('admin.viewingRequest') }}";
        //                 }, 1000);
        //             } else {
        //                 alert("Something went wrong.");
        //             }
        //         })
        //         .catch(error => {
        //             console.error('Error:', error);
        //             alert("An error occurred.");
        //         });
        // }

        function submitAssignedRoom() {
            const form = document.getElementById('assignRoomForm');
            const formData = new FormData(form);

            fetch("{{ route('admin.assignRoomToSlot') }}", {
                    method: "POST",
                    headers: {
                        'X-CSRF-TOKEN': "{{ csrf_token() }}"
                    },
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status) {
                        alert("Room assigned successfully!");
                        window.location.reload();
                    } else {
                        alert("Failed to assign room.");
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert("An error occurred while assigning the room.");
                });
        }
    </script>
    <script>
        function toggleMeetingLink() {
            const container = document.getElementById('meetingLinkContainer');
            container.classList.remove('d-none');
        }
    </script>
    <script>
        $(document).ready(function() {
            var table = $('#viewingRequestTable').DataTable({
                lengthChange: false,
                'columnDefs': [{
                    'targets': [4], // column index (start from 0)
                    'orderable': false, // set orderable false for selected columns
                }]
            });
        });
    </script>
@endpush
