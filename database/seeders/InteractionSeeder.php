<?php

namespace Database\Seeders;

use App\Models\Interaction;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class InteractionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        // Interactions for the first customer
        Interaction::create([
            'customer_id' => 1,
            'user_id' => 2, // John Agent
            'type' => 'meeting',
            'notes' => 'Initial site visit completed. Customer interested in Plot A-101',
            'interaction_date' => now()->subDays(45),
        ]);

        Interaction::create([
            'customer_id' => 1,
            'user_id' => 2,
            'type' => 'call',
            'notes' => 'Follow-up call regarding documentation requirements',
            'interaction_date' => now()->subDays(40),
        ]);

        // Interactions for the second customer
        Interaction::create([
            'customer_id' => 2,
            'user_id' => 3, // Sarah Agent
            'type' => 'email',
            'notes' => 'Sent price quotation for Plot B-202',
            'interaction_date' => now()->subDays(10),
        ]);
    }
}
