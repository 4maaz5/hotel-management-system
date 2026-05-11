<?php

namespace App\Services;

use App\Models\ReportSetting;
use Illuminate\Support\Facades\DB;

class DocumentNumberService
{
    public function generate(string $key): string
    {
        return DB::transaction(function () use ($key) {

            $setting = ReportSetting::where('key', $key)
                ->lockForUpdate()
                ->firstOrFail();

            if ($setting->reset_yearly) {

                $currentYear = now()->year;

                if ($setting->last_reset_year != $currentYear) {
                    $setting->update([
                        'current_sequence' => 1,
                        'last_reset_year' => $currentYear,
                    ]);
                }
            }

            $sequence = str_pad(
                $setting->current_sequence,
                $setting->sequence_length,
                '0',
                STR_PAD_LEFT
            );

            $year = now()->format('Y');

            switch ($setting->naming_method) {

                case 'sequence':
                    $number = $sequence;
                    break;

                case 'year_sequence':
                    $number = $year.$sequence;
                    break;

                case 'prefix_sequence':
                    $number = $setting->prefix.$sequence;
                    break;

                case 'prefix_year_sequence':
                    $number = $setting->prefix.$year.$sequence;
                    break;

                default:
                    $number = $sequence;
            }

            $setting->increment('current_sequence');

            return $number;
        });
    }

    // public function store(Request $request, DocumentNumberService $service)
    // {
    //     $receiptNumber = $service->generate('receipt');

    //     Receipt::create([
    //         'receipt_no' => $receiptNumber,
    //         'amount' => $request->amount
    //     ]);

    //     return redirect()->back()
    //         ->with('success', 'Receipt created');
    // }
}
