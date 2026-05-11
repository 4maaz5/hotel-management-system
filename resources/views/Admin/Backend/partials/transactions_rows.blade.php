     @forelse ($transactions as $transaction)
         <tr id="transactionRow{{ $transaction->id }}">
             <td>{{ $loop->iteration }}</td>
             <td class="text-start">{{ $transaction->type }}</td>
             <td>{{ number_format($transaction->amount, 2) }}</td>
             <td>{{ $transaction->branch->name }}</td>
             <td>{{ $transaction->date }}</td>
             <td>{{ $transaction->description }}</td>
             <td>
                 <a href="#" class="text-secondary editTransactionBtn" data-id="{{ $transaction->id }}"
                     data-type="{{ $transaction->type }}" data-branch="{{ $transaction->branch_id }}"
                     data-amount="{{ $transaction->amount }}" data-date="{{ $transaction->date }}"
                     data-description="{{ $transaction->description }}" title="Edit">
                     <i class="fas fa-edit"></i>
                 </a>

                 <a href="#" class="text-danger deleteTransactionBtn" data-id="{{ $transaction->id }}"
                     title="Delete">
                     <i class="fas fa-trash-alt"></i>
                 </a>
             </td>
         </tr>
     @empty
         <tr>
             {{-- <td colspan="7">No transactions found.</td> --}}
         </tr>
     @endforelse
