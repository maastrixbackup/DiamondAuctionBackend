@if (!$isRoomAlreadyAssigned)
    <div id="successMessage" class="alert d-none" role="alert"></div>
    <form id="assignRoomForm" onsubmit="event.preventDefault(); submitAssignedRoom();">
        @csrf

        {{-- Hidden fields required for controller filtering --}}
        <input type="hidden" name="room_type" value="{{ $roomType }}">
        <input type="hidden" name="start_time" value="{{ $startTime }}">
        <input type="hidden" name="date" value="{{ $date }}">

        <div class="mb-3">
            @foreach ($rooms as $room)
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="rooms[]" value="{{ $room->id }}"
                        id="room_{{ $room->id }}" {{ $room->is_available ? '' : 'disabled' }}
                        onclick="toggleMeetingLink()">
                    <label class="form-check-label" for="room_{{ $room->id }}">
                        {{ $room->room_name }} - {{ $room->is_available ? 'Available' : 'Unavailable' }}
                    </label>
                </div>
            @endforeach
        </div>
        @if ($roomType === 'Virtual' && $room->is_available)
            {{-- Initially hidden; only shown when a virtual room is selected --}}
            <div id="meetingLinkContainer" class="mt-2 d-none">
                <label for="meeting_link" class="form-label">Meeting Link :</label>
                <input type="url" name="meeting_link" id="meeting_link" class="form-control"
                    placeholder="https://example.com/meeting-link" required>
            </div>
        @endif
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
                <th>Status</th>
                {{-- <th>Assign Room</th>
                <th>Action</th> --}}
            </tr>
        </thead>
        <tbody>
            @foreach ($lots as $lot)
                <tr id="lot-row-{{ $lot->id }}">
                    <td>{{ $lot->lot_id }}</td>
                    <td>{{ $lot->bidder_name }}</td>
                    <td>{{ $lot->room_name ?? 'N/A' }}</td>
                    <td>{{ $lot->room_type }}</td>
                    <td>{{ $lot->date_for_reservation }}</td>
                    <td>{{ \Carbon\Carbon::parse($lot->start_time)->format('h:i A') }}</td>
                    <td>
                        @if ($lot->status == 0)
                            <span class="badge bg-warning">Pending</span>
                        @elseif ($lot->status == 1)
                            <span class="badge bg-success">Approved</span>
                        @else
                            <span class="badge bg-danger">Rejected</span>
                        @endif
                    </td>
                    {{-- <td>
                        @if ($lot->status == 0)
                            <div class="d-flex flex-column gap-1">
                                @foreach ($allRooms as $room)
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio"
                                            name="assigned_room[{{ $lot->id }}]"
                                            id="room_{{ $lot->id }}_{{ $room->id }}"
                                            value="{{ $room->id }}">
                                        <label class="form-check-label"
                                            for="room_{{ $lot->id }}_{{ $room->id }}">
                                            {{ $room->name }} ({{ ucfirst($room->type) }})
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <span class="text-muted">-</span>
                        @endif
                    </td>
                    <td>
                        @if ($lot->status == 0)
                            <select name="lot_status[{{ $lot->id }}]" class="form-select form-select-sm">
                                <option value="">-- Select --</option>
                                <option value="1">Approve</option>
                                <option value="2">Reject</option>
                            </select>
                        @else
                            <span class="text-muted"></span>
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
