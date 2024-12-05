<?php

namespace Database\Seeders;

use App\Models\Document;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DocumentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        // Documents for Plot
        Document::create([
            'documentable_type' => 'App\Models\Plot',
            'documentable_id' => 1,
            'title' => 'Property Papers',
            'file_path' => 'documents/plots/plot1_papers.pdf',
            'file_type' => 'pdf',
            'description' => 'Original property documents',
        ]);

        // Documents for Sale
        Document::create([
            'documentable_type' => 'App\Models\Sale',
            'documentable_id' => 1,
            'title' => 'Sale Agreement',
            'file_path' => 'documents/sales/sale1_agreement.pdf',
            'file_type' => 'pdf',
            'description' => 'Signed sale agreement',
        ]);

        // Documents for Customer
        Document::create([
            'documentable_type' => 'App\Models\Customer',
            'documentable_id' => 1,
            'title' => 'ID Proof',
            'file_path' => 'documents/customers/customer1_id.pdf',
            'file_type' => 'pdf',
            'description' => 'Customer ID verification documents',
        ]);
    }
}
