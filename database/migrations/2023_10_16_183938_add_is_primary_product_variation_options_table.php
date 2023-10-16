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
        //
             Schema::table('product_variation_options',function (Blueprint $table){
                 $table->boolean('is_primary');
                 $table->string('color_code')->nullable();
                 $table->string('product_image')->nullable();
                 
             });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('product_variation_options',function (Blueprint $table){
            $table->dropColumn('is_primary');
            $table->dropColumn('color_code');
            $table->dropColumn('product_image');

        });
    }
};
