<?php

namespace App\Console\Commands;

use App\Models\Tenant;
use Illuminate\Console\Command;

class ExpireTenants extends Command
{
    protected $signature = 'tenants:expire';

    protected $description = 'Mark expired tenants as inactive';

    public function handle(): int
    {
        $expiredCount = Tenant::query()
            ->whereDate('end_date', '<', today())
            ->where('status', '!=', 'inactive')
            ->update(['status' => 'inactive']);

        $this->info("Expired {$expiredCount} tenant(s).");

        return self::SUCCESS;
    }
}
