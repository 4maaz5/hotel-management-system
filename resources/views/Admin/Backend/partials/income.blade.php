@forelse($incomes as $income)
    <tr>
        <td>{{ $loop->iteration }}</td>
        <td>{{ $income->branch->name ?? 'N/A' }}</td>
        <td>{{ ucfirst($income->type) }}</td>
        <td>{{ number_format($income->amount, 2) }}</td>
        <td>{{ $income->payment_type ?? '-' }}</td>
        <td>{{ $income->income_date }}</td>
        <td>
            <a href="#" class="text-secondary edit-income-btn" data-toggle="modal" data-target="#editIncomeModal"
                data-id="{{ $income->id }}" data-branch_id="{{ $income->branch_id }}" data-type="{{ $income->type }}"
                data-amount="{{ $income->amount }}" data-payment_type="{{ $income->payment_type }}"
                data-income_date="{{ $income->income_date }}">
                <i class="fas fa-edit"></i>
            </a>
            <a href="#" class="text-danger delete-income-btn" data-toggle="modal" data-target="#deleteIncomeModal"
                data-id="{{ $income->id }}" data-type="{{ $income->type }}" data-amount="{{ $income->amount }}">
                <i class="fas fa-trash-alt"></i>
            </a>
        </td>
    </tr>
@empty
    <tr>
    </tr>
@endforelse
