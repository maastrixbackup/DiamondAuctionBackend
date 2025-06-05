@extends('admin.layouts.app')
@section('title', 'Slots List')

@section('content')
    <div class="row mt-lg-4 pt-lg-4">
        <div class="page-inner">
            <div class="page-header d-flex align-items-center justify-content-between flex-wrap mb-3">
                <div class="d-flex align-items-center gap-3">
                    <h3 class="fw-bold mb-0">Slots</h3>
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
                            <a href="javascript:;">Slots</a>
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
                @endphp
                <div class="col-md-12">
                    <form method="GET" action="{{ route('admin.viewing-slots.index') }}" class="mb-3">
                        <div class="bg-light rounded-4 px-3 py-4 d-flex flex-wrap align-items-center gap-2 shadow-sm">
                            <div class="px-4">
                                <input type="date" name="day" id="day"
                                    class="form-control form-control-sm rounded-pill border-0 shadow-none"
                                    value="{{ $reqDay }}">
                            </div>
                            <div class="px-4">
                                <select name="room" id="room"
                                    class="form-select form-select-sm rounded-pill border-0 shadow-none">
                                    <option value="">-- All Rooms --</option>
                                    @foreach ($rooms as $room)
                                        <option value="{{ $room }}" {{ $roomName == $room ? 'selected' : '' }}>
                                            {{ $room }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="px-2">
                                <button type="submit" class="btn btn-sm btn-outline-secondary rounded-pill px-4">
                                    Filter
                                </button>
                            </div>
                            <div class="px-2">
                                <a href="{{ route('admin.viewing-slots.index') }}"
                                    class="btn btn-sm btn-link text-decoration-none text-muted px-3">Reset</a>
                            </div>
                        </div>
                    </form>

                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h4 class="card-title mb-0">All Slots</h4>
                        </div>
                        @if (session('success'))
                            <div class="alert alert-success" id="success-alert">
                                {{ session('success') }}
                            </div>
                        @endif

                        <div class="card-body">
                            <div class="table-responsive">
                                <table id="basic-datatables" class="display  table table-hover table-bordered">
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
                                            @foreach ($days as $dayObj)
                                                @if (empty($reqDay) || $reqDay == $dayObj['date'])
                                                    <th>{{ $dayObj['name'] }}</th>
                                                @endif
                                            @endforeach
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                            $startTime = strtotime('09:00');
                                            $endTime = strtotime('18:00');
                                            $timeSlots = [];

                                            while ($startTime <= $endTime) {
                                                $timeSlots[] = date('H:i', $startTime); // or 'g:i A' for 12-hour format
                                                $startTime = strtotime('+30 minutes', $startTime);
                                            }

                                            // Normalize bookings as key => booking map
                                            $bookingMap = collect($grouped)->mapWithKeys(function ($item) {
                                                $key =
                                                    $item->room_name .
                                                    '|' .
                                                    $item->start_time .
                                                    '|' .
                                                    $item->date_for_reservation;
                                                return [$key => $item];
                                            });
                                        @endphp

                                        @foreach ($timeSlots as $time)
                                            <tr>
                                                <td>{{ $time }}</td>
                                                @foreach ($days as $day)
                                                    @if (empty($reqDay) || $reqDay == $day['date'])
                                                        <td>
                                                            <div class="d-flex justify-content-center">
                                                                @foreach ($rooms as $rk => $room)
                                                                    @php
                                                                        // Ensure exact match with full key
                                                                        $start_time = $time . ':00'; // match DB time format
                                                                        $date = $day['date']; // from the loop
                                                                        $key = $room . '|' . $start_time . '|' . $date;

                                                                        $booking = $bookingMap[$key] ?? null;

                                                                        $bgClass = 'bg-[#ebf1f5]'; // default gray
                                                                        if ($booking) {
                                                                            $bgClass =
                                                                                $booking->status == 1
                                                                                    ? 'bg-[#c8f7c5]' // light green
                                                                                    : ($booking->status == 2
                                                                                        ? 'bg-[#f8d7da]' // light red
                                                                                        : 'bg-[#ebf1f5]');
                                                                        }
                                                                    @endphp

                                                                    @if (empty($roomName) || $roomName == $room)
                                                                        <div
                                                                            class="col card m-1"@if ($booking) style="min-width: 80px; height:90px;" @else style="width: 30px; height:90px; border:1px dashed #d0d5d9" @endif>
                                                                            <div class="card-body p-1 text-center {{ $bgClass }}">
                                                                                @if ($booking)
                                                                                    <strong>{{ $booking->bidder_name }}</strong><br>

                                                                                    Lot:
                                                                                    {{ implode(', ', $booking->lot_ids) }}
                                                                                @endif

                                                                            </div>
                                                                        </div>
                                                                    @endif
                                                                @endforeach
                                                            </div>
                                                        </td>
                                                    @endif
                                                @endforeach
                                            </tr>
                                        @endforeach

                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
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
