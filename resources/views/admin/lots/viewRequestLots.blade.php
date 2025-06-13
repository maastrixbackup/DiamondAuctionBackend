@if (!$isRoomAlreadyAssigned)
    <div id="successMessage" class="alert d-none" role="alert"></div>
    <form id="assignRoomForm" onsubmit="event.preventDefault(); submitAssignedRoom();">
        @csrf

        {{-- Hidden fields required for controller filtering --}}
        <input type="hidden" name="room_type" value="{{ $roomType }}">
        <input type="hidden" name="start_time" value="{{ $startTime }}">
        <input type="hidden" name="date" value="{{ $date }}">

        {{-- <div class="mb-3 d-flex"> --}}
        {{-- @foreach ($rooms as $room) --}}
        {{-- <div class="form-check">
                    <input class="form-check-input" type="radio" name="rooms[]" value="{{ $room->id }}"
                        id="room_{{ $room->id }}" {{ $room->is_available ? '' : 'disabled' }}
                        onclick="toggleMeetingLink()">
                    <label class="form-check-label" for="room_{{ $room->id }}">
                        {{ $room->room_name }} - {{ $room->is_available ? 'Available' : 'Unavailable' }}
                    </label>
                </div> --}}
        {{-- @endforeach --}}
        {{-- </div> --}}
        {{-- @if ($roomType === 'Virtual' && $room->is_available) --}}
        {{-- Initially hidden; only shown when a virtual room is selected --}}
        {{-- <div id="meetingLinkContainer" class="mt-2 d-none">
                <label for="meeting_link" class="form-label">Meeting Link :</label>
                <input type="url" name="meeting_link" id="meeting_link" class="form-control"
                    placeholder="https://example.com/meeting-link" required>
            </div> --}}
        {{-- @endif --}}

        <div class="mb-3 d-flex flex-wrap">
            @foreach ($rooms as $room)
                @php
                    $disabled = $room->room_type !== $roomType || !$room->is_available;
                @endphp

                <div class="form-check">
                    <input class="form-check-input" type="radio" name="rooms[]" value="{{ $room->id }}"
                        id="room_{{ $room->id }}" {{ $disabled ? 'disabled' : '' }}
                        data-is-virtual="{{ $room->room_type === 'Virtual' ? '1' : '0' }}"
                        onclick="toggleMeetingLink(this)" required>
                    <label class="form-check-label text-sm" for="room_{{ $room->id }}">
                        {{ $room->room_name }}
                        <br>
                        (@if ($disabled && $room->room_type !== $roomType)
                            Disabled
                        @else
                            {{ $room->is_available ? 'Available' : 'Unavailable' }}
                        @endif)
                    </label>
                </div>
            @endforeach
        </div>

        {{-- meeting‑link field --}}
        <div id="meetingLinkContainer" class="mt-2 d-none">
            <label for="meeting_link" class="form-label">Meeting Link :</label>
            <input type="url" name="meeting_link" id="meeting_link" class="form-control"
                placeholder="https://example.com/meeting-link">
        </div>

        <div class="text-start">
            <button type="submit" class="btn btn-primary mb-2 mt-2">Assign Room</button>
        </div>
    </form>
@else
    <div class="alert alert-info">
        Room already assigned to this slot.
    </div>
@endif

<form id="updateSlotForm">
    <div id="successMessage" class="alert alert-success d-none" role="alert"></div>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Lot ID</th>
                <th>Bidder Name</th>
                <th>Room Name</th>
                <th>Room Type</th>
                <th>Date</th>
                <th>Start Time</th>
                {{-- <th>Status</th> --}}
            </tr>
        </thead>
        <tbody>
            @php
                $groupedLots = $lots->groupBy('bidder_id');
            @endphp

            @foreach ($groupedLots as $bidderId => $bidderLots)
                @php
                    $firstLot = $bidderLots->first();
                    $lotIds = $bidderLots->pluck('lot_id')->join(', ');
                @endphp
                <tr>
                    <td>{{ $lotIds }}</td>
                    <td>{{ $firstLot->bidder_name }}</td>
                    <td>{{ $firstLot->room_name ?? 'N/A' }}</td>
                    <td>{{ $firstLot->room_type }}</td>
                    <td>
                        {{ \Carbon\Carbon::parse($firstLot->date_for_reservation)->format('d-m-Y') }}
                    </td>
                    <td>{{ \Carbon\Carbon::parse($firstLot->start_time)->format('h:i A') }}</td>
                    {{-- <td>
                        @if ($firstLot->status == 0)
                            <span class="badge bg-warning">Pending</span>
                        @elseif ($firstLot->status == 1)
                            <span class="badge bg-success">Approved</span>
                        @else
                            <span class="badge bg-danger">Rejected</span>
                        @endif
                    </td> --}}
                </tr>
            @endforeach
        </tbody>
    </table>

    {{-- <div class="text-end mt-3">
        <button type="button" class="btn btn-primary" onclick="submitLotStatuses()">Update Slot</button>
    </div> --}}
</form>
@php
    dd(array_values($booking->requested_lot_id));
    if (!empty($booking) && is_array($booking->requested_lot_id) && count($booking->requested_lot_id) > 0) {
        dd(array_values($booking->requested_lot_id));
        // $requestedLots = SlotBooking::whereIn('lot_id', $booking->requested_lot_id)->where('status', 3)->get();
    }
@endphp
@if (!empty($requestedLots))
    <hr />
    <table class="table table-bordered mt-3">
        <thead>
            <tr>
                <th colspan="3">
                    Requested Lots
                </th>
            </tr>
            <tr>
                <th>Lot ID</th>
                <th>Title</th>
                <th>Type</th>
                <th>Color</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($requestedLots as $req)
                @php
                    $lot = \App\Models\Lot::find($req->lot_id);
                    $bookingSlot = \App\Models\SlotBooking::where('booking_id', $req->booking_id)
                        ->where('lot_id', $req->lot_id)
                        ->first();
                @endphp
                <tr class="text-capitalize">
                    <td>{{ $req->lot_id }}</td>
                    <td class="text-capitalize">{{ $lot->title }}</td>
                    <td class="text-capitalize">{{ $lot->type }}</td>
                    <td class="text-capitalize">{{ $lot->color }}</td>
                    <td>
                        <div class="btn-group">
                            @if ($req->status === 3)
                                <a href="javascript:;"
                                    onclick="setRequestStatus(
                                    '{{ $req->lot_id }}',
                                    '{{ $req->booking_id }}',
                                    '1',
                                    '{{ $loop->iteration }}',
                                    '{{ $req->start_time }}',
                                    '{{ $req->date_for_reservation }}'
                                    )"
                                    class="btn btn-success btn-sm" id="approve-{{ $loop->iteration }}">
                                    Approve
                                </a>
                                <a href="javascript:;"
                                    onclick="setRequestStatus(
                                    '{{ $req->lot_id }}',
                                    '{{ $req->booking_id }}',
                                    '2',
                                    '{{ $loop->iteration }}',
                                    '{{ $req->start_time }}',
                                    '{{ $req->date_for_reservation }}'
                                    )"
                                    class="btn btn-danger btn-sm mx-1" id="reject-{{ $loop->iteration }}">
                                    Reject
                                </a>
                            @elseif ($req->status === 1)
                                <a href="javascript:;" class="btn btn-sm btn-success disabled">Approved</a>
                            @elseif ($req->status === 2)
                                <a href="javascript:;" class="btn btn-sm btn-danger disabled">Rejected</a>
                            @endif
                        </div>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endif
