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

        foreach ($categories as $name => $description) {
            $category = Category::query()->updateOrCreate(
                ['slug' => Str::slug($name)],
                ['name' => $name, 'is_active' => true]
            );

            Product::query()->updateOrCreate(
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
        }
    }
}
