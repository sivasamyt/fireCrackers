<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('gift_box_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('gift_box_id')->constrained('gift_boxes')->cascadeOnDelete();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->unsignedInteger('quantity')->default(1);
            $table->timestamps();

            $table->unique(['gift_box_id', 'product_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gift_box_items');
    }
};
