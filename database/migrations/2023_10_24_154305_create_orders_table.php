<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('unique_id');
            $table->foreignId('customer_id')->constrained('users');
            $table->foreignId('shipping_id')->constrained('shipping_addresses');
            $table->integer('payment_method')->comment('1->COD,2->Bkash,3->Nagad')->default(1);
            $table->double('total_price');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
