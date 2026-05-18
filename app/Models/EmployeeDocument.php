<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class EmployeeDocument extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'type',
        'file_path',
        'issue_date',
        'document_number',
        'expiration_date',
        'image',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function hasStoredFile(): bool
    {
        return $this->storedFileDisk() !== null;
    }

    public function hasStoredImage(): bool
    {
        return $this->storedImageDisk() !== null;
    }

    public function storedFileDisk(): ?string
    {
        return $this->diskContaining($this->file_path);
    }

    public function storedImageDisk(): ?string
    {
        return $this->diskContaining($this->image);
    }

    private function diskContaining(?string $path): ?string
    {
        if (! $path) {
            return null;
        }

        foreach (['local', 'public'] as $disk) {
            if (Storage::disk($disk)->exists($path)) {
                return $disk;
            }
        }

        return null;
    }
}
