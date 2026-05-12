<?php

namespace Database\Seeders;

use App\Models\PrintingOption;
use Illuminate\Database\Seeder;

class PrintingOptionSeeder extends Seeder
{
    public function run(): void
    {
        $options = [
            ['report_key' => 'housekeeping_status', 'report_name' => 'Housekeeping Status'],
            ['report_key' => 'housekeeping_task', 'report_name' => 'Housekeeping Task'],
            ['report_key' => 'invoice', 'report_name' => 'Invoice'],
            ['report_key' => 'receipt_voucher', 'report_name' => 'Receipt Voucher'],
            ['report_key' => 'receipt_voucher_report', 'report_name' => 'Receipt Vouchers Report'],
            ['report_key' => 'promissory_note', 'report_name' => 'Promissory Note'],
            ['report_key' => 'credit_note', 'report_name' => 'Credit Note'],
            ['report_key' => 'drop_cash', 'report_name' => 'Drop Cash Voucher'],
            ['report_key' => 'payment_voucher', 'report_name' => 'Payment Voucher'],
            ['report_key' => 'cash_drawer_balance', 'report_name' => 'Cash Drawer Balance'],
            ['report_key' => 'financial_transactions', 'report_name' => 'Financial Transactions Report'],
            ['report_key' => 'daily_transactions', 'report_name' => 'Daily Transactions Report'],
            ['report_key' => 'trial_balance', 'report_name' => 'Trial Balance Report'],
            ['report_key' => 'tax_report', 'report_name' => 'Tax Report'],
            ['report_key' => 'reservation_balances', 'report_name' => 'Reservation Balances Report'],
            ['report_key' => 'payment_vouchers_report', 'report_name' => 'Payment Vouchers Report'],
            ['report_key' => 'invoices_report', 'report_name' => 'Invoices Report'],
            ['report_key' => 'credit_notes_report', 'report_name' => 'Credit Notes Report'],
            ['report_key' => 'promissory_notes_report', 'report_name' => 'Promissory Notes Report'],
            ['report_key' => 'drop_cash_report', 'report_name' => 'Drop Cash Report'],
            ['report_key' => 'guest_ledger', 'report_name' => 'Guest Ledger'],
            ['report_key' => 'city_ledger', 'report_name' => 'City Ledger'],
            ['report_key' => 'revenue_by_source', 'report_name' => 'Revenue by Source'],
            ['report_key' => 'reservation_revenue', 'report_name' => 'Reservation Revenue'],
            ['report_key' => 'reservation_summary', 'report_name' => 'Reservation Summary'],
            ['report_key' => 'reservation_details', 'report_name' => 'Reservation Details'],
            ['report_key' => 'expected_arrivals', 'report_name' => 'Expected Arrivals'],
            ['report_key' => 'expected_departures', 'report_name' => 'Expected Departures'],
            ['report_key' => 'night_audit_summary', 'report_name' => 'Night Audit Summary'],
            ['report_key' => 'night_audit_history', 'report_name' => 'Night Audit History'],
            ['report_key' => 'occupancy', 'report_name' => 'Occupancy Report'],
            ['report_key' => 'others', 'report_name' => 'Others'],
        ];

        foreach ($options as $option) {
            PrintingOption::updateOrCreate(
                ['report_key' => $option['report_key']],
                ['report_name' => $option['report_name']]
            );
        }
    }
}
