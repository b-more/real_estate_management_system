<?php

namespace Database\Seeders;

use App\Models\Payment;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PaymentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        // Payments for the completed sale
        Payment::create([
            'sale_id' => 1,
            'amount' => 50000,
            'type' => 'deposit',
            'status' => 'completed',
            'due_date' => now()->subDays(30),
            'payment_date' => now()->subDays(30),
            'receipt_number' => 'RCP-001',
            'notes' => 'Initial down payment received',
        ]);

        Payment::create([
            'sale_id' => 1,
            'amount' => 105000,
            'type' => 'final_payment',
            'status' => 'completed',
            'due_date' => now()->subDays(15),
            'payment_date' => now()->subDays(15),
            'receipt_number' => 'RCP-002',
            'notes' => 'Final payment received',
        ]);

        // Payment for the sale in negotiation
        Payment::create([
            'sale_id' => 2,
            'amount' => 20000,
            'type' => 'deposit',
            'status' => 'pending',
            'due_date' => now()->addDays(7),
            'notes' => 'Awaiting initial down payment',
        ]);
    }
}
