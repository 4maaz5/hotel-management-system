<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tenant extends Model
{
    use HasFactory;

    protected $table = 'companies';

    protected $fillable = [
        'name',
        'email',
        'phone',
        'start_date',
        'end_date',
        'subscription_status',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function users()
    {
        return $this->hasMany(User::class, 'company_id');
    }

    public function properties()
    {
        return $this->hasMany(Property::class, 'company_id');
    }

    public function isActiveSubscription(?CarbonInterface $date = null): bool
    {
        $date ??= now()->startOfDay();

        if ($this->subscription_status !== 'active') {
            return false;
        }

        if (! $this->start_date || ! $this->end_date) {
            return false;
        }

        return $date->betweenIncluded(
            $this->start_date->copy()->startOfDay(),
            $this->end_date->copy()->endOfDay()
        );
    }
}
