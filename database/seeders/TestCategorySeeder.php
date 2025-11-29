<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\TestCategory;

class TestCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Hematoloji',
                'code' => 'HEM',
                'description' => 'Hematoloji tahlilleri',
                'sort_order' => 1,
                'is_active' => true,
            ],
            [
                'name' => 'Biyokimya',
                'code' => 'BIO',
                'description' => null,
                'sort_order' => 0,
                'is_active' => true,
            ],
            [
                'name' => 'Idrar',
                'code' => 'IDR',
                'description' => null,
                'sort_order' => 0,
                'is_active' => true,
            ],
            [
                'name' => 'Hormon',
                'code' => 'HOR',
                'description' => null,
                'sort_order' => 0,
                'is_active' => true,
            ],
            [
                'name' => 'Kart Testleri',
                'code' => 'KAT',
                'description' => null,
                'sort_order' => 10,
                'is_active' => true,
            ],
            [
                'name' => 'Gaita',
                'code' => 'GAT',
                'description' => null,
                'sort_order' => 0,
                'is_active' => true,
            ],
            [
                'name' => 'Seroloji',
                'code' => 'SRJ',
                'description' => null,
                'sort_order' => 0,
                'is_active' => true,
            ],
        ];

        foreach ($categories as $category) {
            TestCategory::updateOrCreate(
                ['code' => $category['code']],
                $category
            );
        }
    }
}
