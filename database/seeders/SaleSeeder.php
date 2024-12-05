<?php

namespace Database\Seeders;

use App\Models\Sale;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SaleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        // Create a completed sale
        Sale::create([
            'plot_id' => 1,
            'customer_id' => 1,
            'agent_id' => 2, // J
            'sale_price' => 155000,
            'status' => 'completed',
            'sale_date' => now()->subDays(30),
            'notes' => 'Smooth transaction, all documents verified',
        ]);

        // Create a sale in negotiation
        Sale::create([
            'plot_id' => 2,
            'customer_id' => 2,
            'agent_id' => 3, 
            'sale_price' => 185000,
            'status' => 'negotiation',
            'notes' => 'Customer requesting additional amenities',
        ]);
    }
}
