<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reservation_guests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->nullable()->index();
            $table->foreignId('branch_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('reservation_id')->constrained()->cascadeOnDelete();
            $table->foreignId('guest_id')->constrained()->cascadeOnDelete();
            $table->boolean('is_primary')->default(false);
            $table->string('relationship', 50)->nullable();
            $table->enum('check_in_status', ['pending', 'checked_in', 'no_show'])->default('pending');
            $table->enum('check_out_status', ['pending', 'checked_out'])->default('pending');
            $table->timestamps();

            $table->unique(['reservation_id', 'guest_id']);
            $table->index(['reservation_id', 'is_primary']);
        });

        DB::table('reservations')
            ->whereNotNull('guest_id')
            ->orderBy('id')
            ->chunkById(200, function ($reservations): void {
                $rows = [];

                foreach ($reservations as $reservation) {
                    $checkInStatus = match ($reservation->status) {
                        'checked_in', 'checked_out' => 'checked_in',
                        'no_show' => 'no_show',
                        default => 'pending',
                    };

                    $checkOutStatus = $reservation->status === 'checked_out'
                        ? 'checked_out'
                        : 'pending';

                    $rows[] = [
                        'company_id' => $reservation->company_id,
                        'branch_id' => $reservation->branch_id,
                        'reservation_id' => $reservation->id,
                        'guest_id' => $reservation->guest_id,
                        'is_primary' => true,
                        'relationship' => 'primary',
                        'check_in_status' => $checkInStatus,
                        'check_out_status' => $checkOutStatus,
                        'created_at' => $reservation->created_at ?? now(),
                        'updated_at' => $reservation->updated_at ?? now(),
                    ];
                }

                if ($rows !== []) {
                    DB::table('reservation_guests')->insertOrIgnore($rows);
                }
            });
    }

    public function down(): void
    {
        Schema::dropIfExists('reservation_guests');
    }
};
