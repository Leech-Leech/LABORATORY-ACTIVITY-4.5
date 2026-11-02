<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CatalogSeeder extends Seeder
{
    public function run(): void
    {
        $catalog = [
            'Beverages' => [
                ['Arabica Coffee Beans 250g', 8.50, 100],
                ['Green Tea Box (20 bags)', 4.25, 80],
            ],
            'Snacks' => [
                ['Salted Peanuts 200g', 2.75, 150],
                ['Dark Chocolate Bar', 3.40, 120],
            ],
            'Household' => [
                ['Dish Soap 500ml', 3.10, 90],
                ['Cotton Dish Towels (3-pack)', 6.90, 60],
            ],
        ];

        foreach ($catalog as $categoryName => $products) {
            $category = Category::firstOrCreate(
                ['name' => $categoryName],
                ['description' => "{$categoryName} products"],
            );

            foreach ($products as [$name, $price, $stock]) {
                $category->products()->firstOrCreate(
                    ['name' => $name],
                    ['price' => $price, 'stock_quantity' => $stock, 'is_available' => true],
                );
            }
        }
    }
}
