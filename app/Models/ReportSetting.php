<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;

class ReportSetting extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'key',
        'name',
        'naming_method',
        'prefix',
        'current_sequence',
        'sequence_length',
        'reset_yearly',
        'last_reset_year',
    ];

    public function getNamingMethodLabelAttribute()
    {
        return match ($this->naming_method) {
            'sequence' => 'Sequence No. Only',
            'year_sequence' => 'Year + Sequence No.',
            'prefix_sequence' => 'Prefix + Sequence No.',
            'prefix_year_sequence' => 'Prefix + Year + Sequence No.',
            default => 'Sequence No. Only'
        };
    }

    public function getExampleAttribute()
    {
        $year = now()->format('Y');

        $first = str_pad(
            $this->current_sequence,
            $this->sequence_length,
            '0',
            STR_PAD_LEFT
        );

        $second = str_pad(
            $this->current_sequence + 1,
            $this->sequence_length,
            '0',
            STR_PAD_LEFT
        );

        return match ($this->naming_method) {

            'sequence' => "{$first}, {$second}, ...",

            'year_sequence' => "{$year}{$first}, {$year}{$second}, ...",

            'prefix_sequence' => "{$this->prefix}{$first}, {$this->prefix}{$second}, ...",

            'prefix_year_sequence' => "{$this->prefix}{$year}{$first}, {$this->prefix}{$year}{$second}, ...",

            default => "{$first}, {$second}, ..."
        };
    }
}
