@forelse ($documents as $companyDoc)
    <tr id="companyDocRow{{ $companyDoc->id }}">
        <td>{{ $companyDoc->name }}</td>
        <td>{{ $companyDoc->type }}</td>
        <td>{{ $companyDoc->issued_by }}</td>
        <td>{{ $companyDoc->issue_date }}</td>
        <td>{{ $companyDoc->expiration_date }}</td>
        <td>
            @if ($companyDoc->file_path)
                <a href="#" class="view-pdf" data-file="{{ asset('storage/' . $companyDoc->file_path) }}"
                    title="View PDF">
                    <i class="fas fa-file-pdf text-secondary" style="font-size: 18px;"></i>
                </a>
            @endif
        </td>

        <td>
            <a href="#" class="text-secondary editCompanyDocBtn" data-id="{{ $companyDoc->id }}"
                data-name="{{ $companyDoc->name }}" data-type="{{ $companyDoc->type }}"
                data-issued_by="{{ $companyDoc->issued_by }}" data-issue_date="{{ $companyDoc->issue_date }}"
                data-expiration_date="{{ $companyDoc->expiration_date }}"
                data-file_path="{{ $companyDoc->file_path }}" data-toggle="modal" data-target="#editCompanyDocModal">
                <i class="fas fa-edit"></i>
            </a>

            <a href="#" class="text-danger deleteCompanyDocBtn" data-id="{{ $companyDoc->id }}"
                data-toggle="modal" data-target="#deleteCompanyDocModal">
                <i class="fas fa-trash-alt"></i>
            </a>
        </td>
    </tr>
@empty
    <tr>
        {{-- <td colspan="7" class="text-center">No Documents Found</td> --}}
    </tr>
@endforelse
