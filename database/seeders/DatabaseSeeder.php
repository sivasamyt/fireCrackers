<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        /*
        |--------------------------------------------------------------------------
        | Admin User
        |--------------------------------------------------------------------------
        |
        | IMPORTANT:
        | Set ADMIN_EMAIL and ADMIN_PASSWORD in Laravel Cloud environment variables.
        |
        */

        $adminEmail = env('ADMIN_EMAIL', 'admin@royalcrackers.test');
        $adminPassword = env('ADMIN_PASSWORD', 'password');

        User::query()->updateOrCreate(
            ['email' => $adminEmail],
            [
                'name' => 'Admin',
                'password' => Hash::make($adminPassword),
                'role' => User::ROLE_ADMIN,
                'email_verified_at' => now(),
            ]
        );

        /*
        |--------------------------------------------------------------------------
        | Categories and Products
        |--------------------------------------------------------------------------
        */

        $products = [
            'Sparklers' => [
                'description' => 'Hand-held sparkles for celebrations',
                'price' => 199.00,
                'discount_percent' => 5,
                'stock' => 100,
            ],

            'Flower Pots' => [
                'description' => 'Ground-based colorful fountains',
                'price' => 349.00,
                'discount_percent' => 10,
                'stock' => 80,
            ],

            'Rockets' => [
                'description' => 'Sky rockets and aerial crackers',
                'price' => 499.00,
                'discount_percent' => 10,
                'stock' => 60,
            ],

            'Bombs' => [
                'description' => 'Loud sound crackers',
                'price' => 299.00,
                'discount_percent' => 5,
                'stock' => 75,
            ],

            'Gift Boxes' => [
                'description' => 'Assorted festival packs',
                'price' => 999.00,
                'discount_percent' => 15,
                'stock' => 40,
            ],
        ];

        $regularProducts = collect();

        foreach ($products as $name => $details) {
            $category = Category::query()->updateOrCreate(
                [
                    'slug' => Str::slug($name),
                ],
                [
                    'name' => $name,
                    'is_active' => true,
                ]
            );

            $product = Product::query()->updateOrCreate(
                [
                    'slug' => Str::slug($name) . '-classic',
                ],
                [
                    'category_id' => $category->id,
                    'name' => $name . ' Classic Pack',
                    'description' => $details['description']
                        . '. Premium quality festive firecrackers.',
                    'price' => $details['price'],
                    'discount_percent' => $details['discount_percent'],
                    'stock' => $details['stock'],
                    'is_active' => true,
                ]
            );

            $regularProducts->push($product);
        }

        /*
        |--------------------------------------------------------------------------
        | Combo Category
        |--------------------------------------------------------------------------
        */

        $comboCategory = Category::query()->updateOrCreate(
            [
                'slug' => Product::COMBO_CATEGORY_SLUG,
            ],
            [
                'name' => 'Combo',
                'is_active' => true,
            ]
        );

        /*
        |--------------------------------------------------------------------------
        | Festival Combo Product
        |--------------------------------------------------------------------------
        */

        if ($regularProducts->count() >= 3) {
            $combo = Product::query()->updateOrCreate(
                [
                    'slug' => 'festival-combo-pack',
                ],
                [
                    'category_id' => $comboCategory->id,
                    'name' => 'Festival Combo Pack',
                    'description' => 'A festive mix of popular crackers at a special combo price.',
                    'price' => 1499.00,
                    'discount_percent' => 10,
                    'stock' => 50,
                    'is_active' => true,
                ]
            );

            $sync = [];

            foreach ($regularProducts->take(3) as $index => $component) {
                $sync[$component->id] = [
                    'quantity' => $index === 0 ? 2 : 1,
                ];
            }

            $combo->components()->sync($sync);
        }
    }
}