<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $this->dropForeignKey('reservations', 'payment_method_id');
        $this->dropForeignKey('receipt_vouchers', 'payment_method_id');
        $this->dropForeignKey('promissory_notes', 'payment_method_id');

        $this->normalizePaymentMethodIdsToConfigs('reservations');
        $this->normalizePaymentMethodIdsToConfigs('receipt_vouchers');
        $this->normalizePaymentMethodIdsToConfigs('promissory_notes');
        $this->normalizePaymentMethodIdsToConfigs('payment_vouchers');

        $this->addConfigForeignKey('reservations', 'nullOnDelete');
        $this->addConfigForeignKey('receipt_vouchers', 'set null');
        $this->addConfigForeignKey('promissory_notes', 'set null');
        $this->addConfigForeignKey('payment_vouchers', 'set null');
    }

    public function down(): void
    {
        $this->dropForeignKey('reservations', 'payment_method_id');
        $this->dropForeignKey('receipt_vouchers', 'payment_method_id');
        $this->dropForeignKey('promissory_notes', 'payment_method_id');
        $this->dropForeignKey('payment_vouchers', 'payment_method_id');

        $this->normalizePaymentMethodIdsToMethods('reservations');
        $this->normalizePaymentMethodIdsToMethods('receipt_vouchers');
        $this->normalizePaymentMethodIdsToMethods('promissory_notes');
        $this->normalizePaymentMethodIdsToMethods('payment_vouchers');

        $this->addMethodForeignKey('reservations', 'nullOnDelete');
        $this->addMethodForeignKey('receipt_vouchers', 'set null');
        $this->addMethodForeignKey('promissory_notes', 'set null');
    }

    private function normalizePaymentMethodIdsToConfigs(string $table): void
    {
        if (! Schema::hasTable($table) || ! Schema::hasTable('payment_method_configs')) {
            return;
        }

        $configIds = array_flip(
            DB::table('payment_method_configs')->pluck('id')->map(fn ($id) => (int) $id)->all()
        );

        $configByMethodId = DB::table('payment_method_configs')
            ->pluck('id', 'payment_method_id')
            ->mapWithKeys(fn ($configId, $methodId) => [(int) $methodId => (int) $configId])
            ->all();

        $rows = DB::table($table)
            ->select('id', 'payment_method_id')
            ->whereNotNull('payment_method_id')
            ->get();

        foreach ($rows as $row) {
            $currentId = (int) $row->payment_method_id;

            if (isset($configIds[$currentId])) {
                continue;
            }

            $targetConfigId = $configByMethodId[$currentId] ?? null;

            if (! $targetConfigId && DB::table('payment_methods')->where('id', $currentId)->exists()) {
                $timestamp = now();

                $targetConfigId = DB::table('payment_method_configs')->insertGetId([
                    'payment_method_id' => $currentId,
                    'description' => null,
                    'is_active' => true,
                    'created_at' => $timestamp,
                    'updated_at' => $timestamp,
                ]);

                $configIds[(int) $targetConfigId] = true;
                $configByMethodId[$currentId] = (int) $targetConfigId;
            }

            DB::table($table)
                ->where('id', $row->id)
                ->update([
                    'payment_method_id' => $targetConfigId,
                ]);
        }
    }

    private function normalizePaymentMethodIdsToMethods(string $table): void
    {
        if (! Schema::hasTable($table) || ! Schema::hasTable('payment_method_configs')) {
            return;
        }

        $methodIdByConfigId = DB::table('payment_method_configs')
            ->pluck('payment_method_id', 'id')
            ->mapWithKeys(fn ($methodId, $configId) => [(int) $configId => (int) $methodId])
            ->all();

        $rows = DB::table($table)
            ->select('id', 'payment_method_id')
            ->whereNotNull('payment_method_id')
            ->get();

        foreach ($rows as $row) {
            $configId = (int) $row->payment_method_id;

            DB::table($table)
                ->where('id', $row->id)
                ->update([
                    'payment_method_id' => $methodIdByConfigId[$configId] ?? null,
                ]);
        }
    }

    private function addConfigForeignKey(string $table, string $deleteAction): void
    {
        Schema::table($table, function (Blueprint $tableBlueprint) use ($deleteAction) {
            $foreign = $tableBlueprint
                ->foreign('payment_method_id')
                ->references('id')
                ->on('payment_method_configs');

            if ($deleteAction === 'nullOnDelete') {
                $foreign->nullOnDelete();
            } else {
                $foreign->onDelete($deleteAction);
            }
        });
    }

    private function addMethodForeignKey(string $table, string $deleteAction): void
    {
        Schema::table($table, function (Blueprint $tableBlueprint) use ($deleteAction) {
            $foreign = $tableBlueprint
                ->foreign('payment_method_id')
                ->references('id')
                ->on('payment_methods');

            if ($deleteAction === 'nullOnDelete') {
                $foreign->nullOnDelete();
            } else {
                $foreign->onDelete($deleteAction);
            }
        });
    }

    private function dropForeignKey(string $table, string $column): void
    {
        if (! Schema::hasTable($table)) {
            return;
        }

        try {
            Schema::table($table, function (Blueprint $tableBlueprint) use ($column) {
                $tableBlueprint->dropForeign([$column]);
            });
        } catch (\Throwable $e) {
            // Some environments may already have a custom or missing constraint name.
        }
    }
};
