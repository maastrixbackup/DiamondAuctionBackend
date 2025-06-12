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
                    <td>{{ $firstLot->date_for_reservation }}</td>
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
