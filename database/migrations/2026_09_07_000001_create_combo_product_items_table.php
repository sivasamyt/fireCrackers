<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('combo_product_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('combo_product_id')->constrained('products')->cascadeOnDelete();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->unsignedInteger('quantity')->default(1);
            $table->timestamps();

            $table->unique(['combo_product_id', 'product_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('combo_product_items');
    }
};
