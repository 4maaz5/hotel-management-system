<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Leave extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'leave_type',
        'start_date',
        'end_date',
        'total_days',
        'payment_type',
        'travel_responsibility',
        'status',
        'reason',
        'ticket_amount',
    ];

    public function documents()
    {
        return $this->hasMany(LeaveDocument::class);
    }

    protected static function booted()
    {
        static::deleting(function ($leave) {
            // delete all associated files from storage
            foreach ($leave->documents as $doc) {
                if (Storage::disk('public')->exists($doc->file_path)) {
                    Storage::disk('public')->delete($doc->file_path);
                }
                $doc->delete(); // delete DB record
            }
        });
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
