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
        $adminEmail = env('ADMIN_EMAIL', 'admin@firecrackers.test');
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

        $categories = [
            'Sparklers' => 'Hand-held sparkles for celebrations',
            'Flower Pots' => 'Ground-based colorful fountains',
            'Rockets' => 'Sky rockets and aerial crackers',
            'Bombs' => 'Loud sound crackers',
            'Gift Boxes' => 'Assorted festival packs',
        ];

        $regularProducts = collect();

        foreach ($categories as $name => $description) {
            $category = Category::query()->updateOrCreate(
                ['slug' => Str::slug($name)],
                ['name' => $name, 'is_active' => true]
            );

            $product = Product::query()->updateOrCreate(
                ['slug' => Str::slug($name).'-classic'],
                [
                    'category_id' => $category->id,
                    'name' => $name.' Classic Pack',
                    'description' => $description.'. Premium quality festive firecrackers.',
                    'price' => fake()->randomFloat(2, 99, 999),
                    'discount_percent' => fake()->randomElement([0, 5, 10, 15]),
                    'stock' => fake()->numberBetween(20, 200),
                    'is_active' => true,
                ]
            );

            $regularProducts->push($product);
        }

        $comboCategory = Category::query()->updateOrCreate(
            ['slug' => Product::COMBO_CATEGORY_SLUG],
            ['name' => 'Combo', 'is_active' => true]
        );

        if ($regularProducts->count() >= 2) {
            $combo = Product::query()->updateOrCreate(
                ['slug' => 'festival-combo-pack'],
                [
                    'category_id' => $comboCategory->id,
                    'name' => 'Festival Combo Pack',
                    'description' => 'A festive mix of popular crackers at a special combo price.',
                    'price' => 1499,
                    'discount_percent' => 10,
                    'stock' => 50,
                    'is_active' => true,
                ]
            );

            $sync = [];
            foreach ($regularProducts->take(3) as $index => $component) {
                $sync[$component->id] = ['quantity' => $index === 0 ? 2 : 1];
            }
            $combo->components()->sync($sync);
        }
    }
}
