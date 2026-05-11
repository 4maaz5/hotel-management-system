<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            // Remove columns
            if (Schema::hasColumn('products', 'warehouse_id')) {
                $table->dropForeign(['warehouse_id']);
                $table->dropColumn('warehouse_id');
            }

            if (Schema::hasColumn('products', 'room_id')) {
                $table->dropForeign(['room_id']);
                $table->dropColumn('room_id');
            }

            if (Schema::hasColumn('products', 'stock')) {
                $table->dropColumn('stock');
            }

            if (Schema::hasColumn('products', 'price')) {
                $table->dropColumn('price');
            }

            // Add new columns
            if (! Schema::hasColumn('products', 'sku')) {
                $table->string('sku')->nullable()->after('category_id');
            }

            if (! Schema::hasColumn('products', 'unit')) {
                $table->string('unit')->default('pcs')->after('sku');
            }
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            // Revert: add back removed columns
            if (! Schema::hasColumn('products', 'warehouse_id')) {
                $table->foreignId('warehouse_id')->constrained()->onDelete('cascade')->after('category_id');
            }

            if (! Schema::hasColumn('products', 'room_id')) {
                $table->foreignId('room_id')->nullable()->constrained()->onDelete('set null')->after('warehouse_id');
            }

            if (! Schema::hasColumn('products', 'stock')) {
                $table->integer('stock')->default(0)->after('room_id');
            }

            if (! Schema::hasColumn('products', 'price')) {
                $table->decimal('price', 10, 2)->nullable()->after('stock');
            }

            // Remove added columns
            $table->dropColumn(['sku', 'unit']);
        });
    }
};
