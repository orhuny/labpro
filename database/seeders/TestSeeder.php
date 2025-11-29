<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Test;
use App\Models\TestCategory;

class TestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get categories by code for easier reference
        $categories = TestCategory::all()->keyBy('code');

        $tests = [
            [
                'category_code' => 'HEM',
                'name' => 'Hematoloji',
                'code' => 'HTT',
                'description' => null,
                'price' => 0.00,
                'turnaround_time_hours' => null,
                'is_active' => true,
                'sort_order' => 0,
            ],
            [
                'category_code' => 'IDR',
                'name' => 'Idrar Tahlili',
                'code' => 'IDRAR',
                'description' => null,
                'price' => 0.00,
                'turnaround_time_hours' => null,
                'is_active' => true,
                'sort_order' => 0,
            ],
            [
                'category_code' => 'HOR',
                'name' => 'Hormon Testi',
                'code' => 'HOR',
                'description' => null,
                'price' => 0.00,
                'turnaround_time_hours' => null,
                'is_active' => true,
                'sort_order' => 0,
            ],
            [
                'category_code' => 'KAT',
                'name' => 'Kart Testleri',
                'code' => 'KAR',
                'description' => null,
                'price' => 0.00,
                'turnaround_time_hours' => null,
                'is_active' => true,
                'sort_order' => 0,
            ],
            [
                'category_code' => 'SRJ',
                'name' => 'Seroloji',
                'code' => 'SRJ',
                'description' => null,
                'price' => 0.00,
                'turnaround_time_hours' => null,
                'is_active' => true,
                'sort_order' => 0,
            ],
            [
                'category_code' => 'GAT',
                'name' => 'Gaita',
                'code' => 'GAT',
                'description' => null,
                'price' => 0.00,
                'turnaround_time_hours' => null,
                'is_active' => true,
                'sort_order' => 0,
            ],
            [
                'category_code' => 'BIO',
                'name' => 'Biyokimya',
                'code' => 'BIO',
                'description' => null,
                'price' => 0.00,
                'turnaround_time_hours' => null,
                'is_active' => true,
                'sort_order' => 0,
            ],
        ];

        foreach ($tests as $testData) {
            $category = $categories->get($testData['category_code']);
            
            if (!$category) {
                $this->command->warn("Category with code '{$testData['category_code']}' not found. Skipping test '{$testData['name']}'.");
                continue;
            }

            Test::updateOrCreate(
                ['code' => $testData['code']],
                [
                    'test_category_id' => $category->id,
                    'name' => $testData['name'],
                    'description' => $testData['description'],
                    'price' => $testData['price'],
                    'turnaround_time_hours' => $testData['turnaround_time_hours'],
                    'is_active' => $testData['is_active'],
                    'sort_order' => $testData['sort_order'],
                ]
            );
        }
    }
}

