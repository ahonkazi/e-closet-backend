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
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('notification_type_id')->constrained('notification_types');
            $table->boolean('read_status')->default(false);
            $table->foreignId('from_id')->constrained('users');
            $table->integer('receiver_id')->nullable();
            $table->foreignId('receiver_role_id')->constrained('user_roles');
            $table->integer('ref_id');
            $table->string('tamplate');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
