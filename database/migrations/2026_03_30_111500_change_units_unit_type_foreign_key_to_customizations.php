<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $this->ensureCustomizationRowsForExistingUnits();

        Schema::table('units', function (Blueprint $table) {
            $table->dropForeign(['unit_type_id']);
        });

        $this->remapUnitsToCustomizationIds();

        Schema::table('units', function (Blueprint $table) {
            $table->foreign('unit_type_id')
                ->references('id')
                ->on('unit_type_customizations')
                ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('units', function (Blueprint $table) {
            $table->dropForeign(['unit_type_id']);
        });

        $customizationMap = DB::table('unit_type_customizations')
            ->pluck('unit_type_id', 'id');

        foreach ($customizationMap as $customizationId => $unitTypeId) {
            DB::table('units')
                ->where('unit_type_id', $customizationId)
                ->update(['unit_type_id' => $unitTypeId]);
        }

        Schema::table('units', function (Blueprint $table) {
            $table->foreign('unit_type_id')
                ->references('id')
                ->on('unit_types')
                ->cascadeOnDelete();
        });
    }

    private function ensureCustomizationRowsForExistingUnits(): void
    {
        $referencedUnitTypeIds = DB::table('units')
            ->distinct()
            ->pluck('unit_type_id')
            ->filter()
            ->values();

        if ($referencedUnitTypeIds->isEmpty()) {
            return;
        }

        $existingCustomizationUnitTypeIds = DB::table('unit_type_customizations')
            ->whereIn('unit_type_id', $referencedUnitTypeIds)
            ->pluck('unit_type_id')
            ->all();

        $missingUnitTypeIds = $referencedUnitTypeIds
            ->reject(fn ($id) => in_array($id, $existingCustomizationUnitTypeIds, true))
            ->values();

        if ($missingUnitTypeIds->isEmpty()) {
            return;
        }

        $now = now();

        $unitTypes = DB::table('unit_types')
            ->whereIn('id', $missingUnitTypeIds)
            ->get(['id', 'name']);

        foreach ($unitTypes as $unitType) {
            DB::table('unit_type_customizations')->insert([
                'unit_type_id' => $unitType->id,
                'name' => $unitType->name,
                'unit_area' => null,
                'single_beds' => 0,
                'double_beds' => 0,
                'base_occupancy' => 1,
                'description' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }

    private function remapUnitsToCustomizationIds(): void
    {
        $customizationMap = DB::table('unit_type_customizations')
            ->select('id', 'unit_type_id')
            ->orderBy('id')
            ->get()
            ->groupBy('unit_type_id')
            ->map(fn ($rows) => $rows->first()->id);

        foreach ($customizationMap as $unitTypeId => $customizationId) {
            DB::table('units')
                ->where('unit_type_id', $unitTypeId)
                ->update(['unit_type_id' => $customizationId]);
        }
    }
};
