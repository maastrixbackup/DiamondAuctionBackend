{{-- <table class="table table-bordered">
    <thead>
        <tr>
            <th>Lot ID</th>
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
        @foreach ($lots as $lot)
            <tr id="lot-row-{{ $lot->id }}">
                <td>{{ $lot->lot_id }}</td>
                <td>{{ $lot->bidder_name }}</td>
                <td>{{ $lot->room_name }}</td>
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
                <td>
                    @if ($lot->status == 0)
                        <button class="btn btn-success btn-sm" onclick="updateStatus({{ $lot->id }}, 1)">Approve</button>
                        <button class="btn btn-danger btn-sm mt-1" onclick="updateStatus({{ $lot->id }}, 2)">Reject</button>
                    @else
                        <span class="text-muted">No Action</span>
                    @endif
                </td>
            </tr>
        @endforeach
    </tbody>
</table> --}}

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
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($lots as $lot)
                <tr id="lot-row-{{ $lot->id }}">
                    <td>{{ $lot->lot_id }}</td>
                    <td>{{ $lot->bidder_name }}</td>
                    <td>{{ $lot->room_name }}</td>
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
                    </td>
                </tr>
            @endforeach
        </tbody>

    </table>

    <div class="text-end mt-3">
        <button type="button" class="btn btn-primary" onclick="submitLotStatuses()">Update Slot</button>
    </div>
</form>
