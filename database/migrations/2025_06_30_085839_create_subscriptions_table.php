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
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('office_id')->constrained()->onDelete('cascade');
            $table->enum('subscription_type', ['monthly', 'yearly']);

             $table->decimal('price', 8, 2); // ← السعر حسب النوع
             $table->timestamp('starts_at')->nullable(); // يبدأ بعد الموافقة
             $table->timestamp('expires_at')->nullable();
             $table->enum('status', ['active', 'expired', 'cancelled','pending'])->default('pending');
            $table->timestamps();
        });
    }



    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscriptions');
    }
};
