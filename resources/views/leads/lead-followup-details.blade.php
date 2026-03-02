<table class="table table-bordered mb-0 mt-2">
    <thead>
        <tr>
            <th>#</th>
            <th>Discussion Date</th>
            <th>Follow Up</th>
            <th>Notes</th>
            <th>Created By</th>
        </tr>
    </thead>
    <tbody id="response">
        @if($lead_followup_details->count() > 0)
            @foreach(array_reverse($lead_followup_details->toArray()) as $key => $followup)
            <tr>
                <td>{{$key + 1}}</td>
                <td>{{ $followup['lead_discussion_date'] ? getFormatedDate($followup['lead_discussion_date']) : '' }}</td>
                <td>{{ getFormatedDate($followup['followup_datetime']) }}</td>
                <td>{!! $followup['followup_notes'] !!}</td>
                <td>{{ $followup['lead_created_by'] ? $followup['lead_created_by'] : '' }}</td>
            </tr>
            @endforeach
        @else
            <tr class="table-danger text-center">
                <td colspan="3">
                    No data available
                </td>
            </tr>
        @endif
    </tbody>
</table>