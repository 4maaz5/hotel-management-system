<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('marketing_agents', function (Blueprint $table) {
            $table->id();

            // Relations
            $table->foreignId('company_id')
                ->constrained('companies')
                ->cascadeOnDelete();

            $table->foreignId('brand_id')
                ->nullable()
                ->constrained('brands')
                ->nullOnDelete();

            $table->foreignId('branch_id')
                ->nullable()
                ->constrained('branches')
                ->nullOnDelete();

            // Agent Info
            $table->string('name');
            $table->string('contact_person')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->text('address')->nullable();

            // Type & Status
            $table->enum('type', ['agent', 'company'])->default('agent');
            $table->decimal('commission_percent', 5, 2)->default(5);

            $table->timestamps();

            // Indexes (performance)
            $table->index(['company_id', 'brand_id', 'branch_id']);
            $table->index('type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('marketing_agents');
    }
};
