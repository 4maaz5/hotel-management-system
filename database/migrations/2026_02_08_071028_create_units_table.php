<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('units', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->nullable()->index();
            $table->foreignId('branch_id')->nullable()->constrained('branches')->nullOnDelete();

            $table->string('unit_number')->unique();

            $table->foreignId('unit_class_id')
                ->constrained('unit_classes')
                ->cascadeOnDelete();

            $table->foreignId('unit_type_id')
                ->constrained('unit_types')
                ->cascadeOnDelete();

            $table->boolean('can_be_merged')->default(false);

            $table->foreignId('block_id')
                ->constrained('blocks')
                ->cascadeOnDelete();

            $table->foreignId('floor_id')
                ->constrained('floors')
                ->cascadeOnDelete();

            $table->string('phone_extension')->nullable();

            $table->unsignedTinyInteger('number_of_toilets')->default(1);

            $table->string('kitchen_type')->nullable();

            $table->foreignId('hall_type_id')
                ->nullable()
                ->constrained('hall_types')
                ->nullOnDelete();

            $table->decimal('unit_area', 8, 2)->nullable();

            $table->unsignedTinyInteger('number_of_single_beds')->default(0);
            $table->unsignedTinyInteger('number_of_double_beds')->default(0);

            $table->unsignedTinyInteger('base_occupancy')->default(1);

            $table->text('description')->nullable();

            $table->boolean('is_active')->default(true);
            $table->enum('housekeeping_status', ['clean', 'dirty', 'inspected', 'out_of_service'])
                ->default('clean');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('units');
    }
};
