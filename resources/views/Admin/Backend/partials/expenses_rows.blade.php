@forelse($expenses as $expense)
    <tr id="expenseRow{{ $expense->id }}">
        <td>{{ $expense->item_name }}</td>
        <td>{{ $expense->branch->name ?? '-' }}</td>
        <td>{{ number_format($expense->amount, 2) }}</td>
        <td>{{ $expense->expense_date }}</td>
        <td>{{ $expense->description ?? '-' }}</td>
        <td class="text-center">
            <a href="#" class="text-secondary editExpenseBtn" data-id="{{ $expense->id }}"
                data-item_name="{{ $expense->item_name }}" data-quantity="{{ $expense->quantity }}"
                data-invoice-number="{{ $expense->invoice_number }}" data-branch="{{ $expense->branch_id }}"
                data-amount="{{ $expense->amount }}" data-expense_date="{{ $expense->expense_date }}"
                data-description="{{ $expense->description }}">
                <i class="fas fa-edit"></i>
            </a>
            <a href="#" class="text-danger deleteExpenseBtn" data-id="{{ $expense->id }}">
                <i class="fas fa-trash-alt"></i>
            </a>
        </td>
    </tr>
@empty
    <tr>
        {{-- <td colspan="6" class="text-center text-muted">No records found.</td> --}}
    </tr>
@endforelse
