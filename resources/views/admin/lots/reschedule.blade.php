@extends('admin.layouts.app')
@section('title', 'Reschedule')

@section('content')
    <div class="row mt-lg-4 pt-lg-4">
        <div class="page-inner">
            <div class="page-header d-flex align-items-center justify-content-between flex-wrap mb-3">
                <div class="d-flex align-items-center gap-3">
                    <h3 class="fw-bold mb-0">Reschedule</h3>
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
                            <a href="javascript:;">Reschedule</a>
                        </li>
                    </ul>
                </div>
            </div>


            <div class="row">
                @php
                    $rooms = [];
                    for ($i = 1; $i <= 7; $i++) {
                        $rooms[] = 'Room ' . $i;
                    }

                    $startTime = strtotime('09:00');
                    $endTime = strtotime('18:00');
                    $timeSlots = [];

                    while ($startTime <= $endTime) {
                        $timeSlots[] = date('H:i', $startTime); // or 'g:i A' for 12-hour format
                        $startTime = strtotime('+30 minutes', $startTime);
                    }
                @endphp
                <div class="col-md-12">
                    <form method="GET" action="{{ route('admin.reschedule-booking', $booking->booking_id) }}"
                        class="mb-4">
                        <div class="bg-light rounded-4 px-4 py-4 d-flex flex-wrap align-items-center gap-3 shadow-sm">
                            <div class="px-2" style="min-width: 200px;">
                                <input type="date" name="day" id="day"
                                    class="form-control form-control-sm rounded-pill border-0 shadow-none"
                                    value="{{ $reqDay }}">
                            </div>
                            <div class="px-2" style="min-width: 200px;">
                                <select name="time" id="time"
                                    class="form-select form-select-sm rounded-pill border-0 shadow-none" required>
                                    <option value="" selected disabled>-- Time --</option>
                                    @foreach ($timeSlots as $time)
                                        <option value="{{ $time }}" {{ $timeFrame == $time ? 'selected' : '' }}>
                                            {{ $time }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="px-2">
                                <button type="submit" class="btn btn-sm btn-outline-secondary rounded-pill px-4">
                                    Filter
                                </button>
                            </div>
                            <div class="px-2">
                                <a href="{{ route('admin.reschedule-booking', $booking->booking_id) }}"
                                    class="btn btn-sm btn-link text-decoration-none text-muted px-3">
                                    Reset
                                </a>
                            </div>
                        </div>
                    </form>

                    @if (!empty($reqDay) || !empty($timeFrame))
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <div class="card-title mb-0">Available Rooms</div>
                            </div>
                            @if (session('success'))
                                <div class="alert alert-success" id="success-alert">
                                    {{ session('success') }}
                                </div>
                            @endif

                            <div class="card-body">
                                <div class="table-responsive">
                                    <table id="basic-datatables" class="display  table table-bordered">
                                        <thead>
                                            @php
                                                $days = [];
                                                for ($i = 0; $i < 7; $i++) {
                                                    $days[] = [
                                                        'name' => date('l', strtotime("+$i day")),
                                                        'date' => date('Y-m-d', strtotime("+$i day")),
                                                    ];
                                                }
                                            @endphp

                                            <tr>
                                                <th>Timing</th>
                                                @if (!empty($reqDay))
                                                    <th>{{ \Carbon\Carbon::parse($reqDay)->format('l') }} -
                                                        {{ \Carbon\Carbon::parse($reqDay)->format('d-m-Y') }}
                                                    </th>
                                                @endif

                                            </tr>
                                        </thead>
                                        <tbody>
                                            <form action="{{ route('admin.re-assign-room', $booking->id) }}" method="post"
                                                id="reAssignRoomForm">
                                                @csrf
                                                <tr>
                                                    @if (!empty($timeFrame))
                                                        <td>
                                                            {{ $timeFrame }}

                                                        </td>
                                                    @endif
                                                    <td>
                                                        <input type="hidden" name="re_date" value="{{ $reqDay }}">
                                                        <input type="hidden" name="re_time" value="{{ $timeFrame }}">
                                                        <div class="d-flex justify-content-center align-items-center">
                                                            @foreach ($rooms as $rk => $room)
                                                                @php
                                                                    $roomNumber = str_replace('Room ', '', $room);
                                                                    $isVisible = in_array($roomNumber, $roomIds);

                                                                    // ✅ Condition to control room visibility based on booking type
                                                                    $showRoom = true;
                                                                    if (
                                                                        $booking->room_type === 'Physical' &&
                                                                        $roomNumber == 7
                                                                    ) {
                                                                        $showRoom = false;
                                                                    } elseif (
                                                                        $booking->room_type === 'Virtual' &&
                                                                        $roomNumber != 7
                                                                    ) {
                                                                        $showRoom = false;
                                                                    }

                                                                    // Styling
                                                                    $bgClr = $isVisible
                                                                        ? 'bg-[#ebf1f5]'
                                                                        : 'bg-[#f8d7da]';
                                                                    $textClr = $isVisible
                                                                        ? 'text-[#065f46]'
                                                                        : 'text-[#842029]';
                                                                    $borderClr = $isVisible
                                                                        ? 'border-[#065f46]'
                                                                        : 'border-[#842029]';
                                                                @endphp

                                                                @if ($showRoom)
                                                                    <div class="col card m-1 border {{ $borderClr }} rounded-md shadow-sm"
                                                                        style="min-width: 60px; height: 70px;">
                                                                        <div
                                                                            class="card-body p-2 text-center {{ $bgClr }} {{ $textClr }} text-sm rounded-md">
                                                                            @if ($isVisible)
                                                                                <label class="block cursor-pointer">
                                                                                    <input type="radio" name="room"
                                                                                        value="{{ $room }}"
                                                                                        class="accent-[#065f46] mb-1"><br />
                                                                                    {{ $room }}
                                                                                </label>
                                                                            @else
                                                                                <div class="font-semibold text-xs mt-2">
                                                                                    Reserved</div>
                                                                                <div class="text-xs">{{ $room }}
                                                                                </div>
                                                                            @endif
                                                                        </div>
                                                                    </div>
                                                                @endif
                                                            @endforeach


                                                        </div>
                                                    </td>
                                                </tr>
                                                @if (!empty($roomIds))
                                                    <tr>
                                                        <td colspan="2" class="text-right">
                                                            <button class="btn btn-primary" type="submit">Re-Assign
                                                                Room</button>
                                                        </td>
                                                    </tr>
                                                @endif
                                            </form>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

@endsection
@push('scripts')
    <script>
        window.onload = function() {
            let alert = document.getElementById('success-alert');
            if (alert) {
                setTimeout(function() {
                    alert.style.transition = 'opacity 0.5s ease';
                    alert.style.opacity = '0';
                    setTimeout(() => alert.remove(), 500); // remove after fade out
                }, 3000); // 3 seconds
            }
        };

        document.getElementById("reAssignRoomForm").addEventListener("submit", function(event) {
            event.preventDefault();

            const selectedRoom = document.querySelector('input[name="room"]:checked');
            if (!selectedRoom) {
                alert('Please select at least one room.');
                return;
            }

            // ✅ If a room is selected, you can safely submit the form
            this.submit();
        });
    </script>

    {{-- <script>
        document.addEventListener('DOMContentLoaded', function() {
            const today = new Date();
            const sixDaysLater = new Date();
            sixDaysLater.setDate(today.getDate() + 6);

            flatpickr("#day", {
                dateFormat: "Y-m-d", // Forces YYYY-MM-DD
                minDate: today,
                maxDate: sixDaysLater,
                defaultDate: today
            });
        });
    </script> --}}


    <script>
        const dateInput = document.getElementById('day');

        const today = new Date();
        const sixDaysLater = new Date();
        sixDaysLater.setDate(today.getDate() + 6);

        // Format to YYYY-MM-DD
        const formatDate = (date) => {
            const year = date.getFullYear();
            const month = String(date.getMonth() + 1).padStart(2, '0');
            const day = String(date.getDate()).padStart(2, '0');
            return `${year}-${month}-${day}`;
        };

        dateInput.min = formatDate(today);
        dateInput.max = formatDate(sixDaysLater);
    </script>
@endpush
