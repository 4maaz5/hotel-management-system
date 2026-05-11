@if (isset($branches) && count($branches) > 0)
    @foreach ($branches as $branch)
        <tr id="branch-row-{{ $branch->id }}">
            <td class="branch-name-cell">{{ $branch->name }}</td>
            <td class="branch-location-cell">{{ $branch->location }}</td>
            <td class="branch-manager-cell">{{ $branch->manager }}</td>
            <td class="branch-email-cell">{{ $branch->email }}</td>
            <td class="branch-phone-cell">{{ $branch->phone }}</td>
            <td class="branch-status-cell">
                @if ($branch->status == 'Active')
                    <span class="badge badge-success">{{ $branch->status }}</span>
                @else
                    <span class="badge badge-danger">{{ $branch->status }}</span>
                @endif
            </td>
            <td>
                <a href="#" class="text-info viewBranchBtn" data-id="{{ $branch->id }}"
                    data-name="{{ $branch->name }}" data-location="{{ $branch->location }}"
                    data-manager="{{ $branch->manager }}" data-email="{{ $branch->email }}"
                    data-phone="{{ $branch->phone }}" data-status="{{ $branch->status }}"
                    data-market_price="{{ $branch->market_price }}" data-rent="{{ $branch->total_rent }}"
                    data-sale_price="{{ $branch->sale_price }}" data-rent_start_date="{{ $branch->rent_start_date }}"
                    data-rent_end_date="{{ $branch->rent_end_date }}"
                    data-damage_assist="{{ $branch->damage_assist }}">
                    <i class="fas fa-eye"></i>
                </a>
                <a href="#" class="text-secondary editBranchBtn" data-id="{{ $branch->id }}"
                    data-name="{{ $branch->name }}" data-location="{{ $branch->location }}"
                    data-manager="{{ $branch->manager }}" data-email="{{ $branch->email }}"
                    data-phone="{{ $branch->phone }}" data-market_price="{{ $branch->market_price }}"
                    data-sale_price="{{ $branch->sale_price }}" data-rent="{{ $branch->total_rent }}"
                    data-total_rent="{{ $branch->total_rent }}" data-installments="{{ $branch->installments }}"
                    data-building_type="{{ $branch->building_type }}"
                    data-rent_start_date="{{ $branch->rent_start_date }}"
                    data-rent_end_date="{{ $branch->rent_end_date }}"
                    data-damage_assist="{{ $branch->damage_assist }}" data-status="{{ $branch->status }}"
                    data-start_date="{{ $branch->documents->first()?->issue_date ?? '-' }}"
                    data-end_date="{{ $branch->documents->first()?->expiration_date ?? '-' }}"
                    data-doc_name="{{ $branch->documents->first()?->name }}">
                    <i class="fas fa-edit"></i>
                </a>
                <a href="#" class="text-danger deleteBranchBtn" data-id="{{ $branch->id }}">
                    <i class="fas fa-trash-alt"></i>
                </a>
            </td>
        </tr>
    @endforeach
@else
    <tr>
        <td colspan="7" class="text-center py-4">
            <div class="empty-state">
                <i class="fas fa-store-alt fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">No branches found</h5>
            </div>
        </td>
    </tr>
@endif
