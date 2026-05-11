@forelse ($notifications as $index => $n)
    <tr id="notificationRow{{ $n->id }}">
        <td>{{ $index + 1 }}</td>
        <td>{{ ucfirst($n->recipient_type) }}</td>
        <td>{{ strtoupper($n->type) }}</td>
        <td>{{ $n->message }}</td>

        <td>
            @if ($n->status === 'sent')
                <span class="badge bg-success">{{ __('dashboard.sent') }}</span>
            @elseif($n->status === 'pending')
                <span class="badge bg-warning">{{ __('dashboard.pending') }}</span>
            @else
                <span class="badge bg-danger">{{ __('dashboard.failed') }}</span>
            @endif
        </td>

        <td>
            {{-- @if ($n->status !== 'sent')
                <a href="#" class="text-secondary editAlertBtn" data-toggle="modal" data-target="#editAlertModal">
                    <i class="fas fa-edit"></i>
                </a>
            @endif --}}

            <a href="#" class="text-danger deleteAlertBtn" data-id="{{ $n->id }}">
                <i class="fas fa-trash-alt"></i>
            </a>
        </td>
    </tr>
@empty
    <tr>
    </tr>
@endforelse
