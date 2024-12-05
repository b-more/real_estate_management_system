<?php

namespace Database\Seeders;

use App\Models\Plot;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PlotSeeder extends Seeder
{
    public function run()
    {
        $plots = [
            [
                'plot_number' => 'A-101',
                'size' => 1200,
                'size_unit' => 'sq_mt',
                'price' => 150000,
                'location' => 'North Zone',
                'address' => '123 North Street, Plot City',
                'amenities' => ['water', 'electricity', 'road_access'],
                'description' => 'Prime location corner plot with excellent connectivity',
                'legal_status' => 'clear',
                'status' => 'available',
                'coordinates' => ['lat' => 12.9716, 'lng' => 77.5946],
            ],
            [
                'plot_number' => 'B-202',
                'size' => 1500,
                'size_unit' => 'sq_mt',
                'price' => 180000,
                'location' => 'South Zone',
                'address' => '456 South Avenue, Plot City',
                'amenities' => ['water', 'electricity', 'road_access', 'park_view'],
                'description' => 'Beautiful park-facing plot in peaceful locality',
                'legal_status' => 'clear',
                'status' => 'available',
                'coordinates' => ['lat' => 12.9725, 'lng' => 77.5945],
            ],
            // Add more plots with different statuses
            [
                'plot_number' => 'C-303',
                'size' => 2000,
                'size_unit' => 'sq_mt',
                'price' => 250000,
                'location' => 'East Zone',
                'address' => '789 East Road, Plot City',
                'amenities' => ['water', 'electricity', 'road_access', 'commercial'],
                'description' => 'Commercial plot in developing area',
                'legal_status' => 'clear',
                'status' => 'reserved',
                'coordinates' => ['lat' => 12.9730, 'lng' => 77.5950],
            ],
        ];

        foreach ($plots as $plot) {
            Plot::create($plot);
        }
    }
}
