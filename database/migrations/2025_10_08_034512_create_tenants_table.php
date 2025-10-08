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
        // Tenants Table
        Schema::create('tenants', function (Blueprint $table) {
            $table->id();
            $table->string('first_name');
            $table->string('last_name');
            $table->unsignedTinyInteger('gender')->comment('1: Male, 2: Female, 3: Other')->nullable();
            $table->string('email')->unique();
            $table->string('phone_number')->unique();
            $table->string('address')->nullable();
            $table->timestamp('joined_at')->nullable();
            $table->timestamps();
        });

        // Rent_Payments Table
        Schema::create('rent_payments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id');
            $table->unsignedBigInteger('room_id');
            $table->decimal('amount_paid', 10, 2)->default(0.00);
            $table->timestamp('payment_date')->nullable();
            $table->unsignedSmallInteger('payment_status')->default(1)->comment('1: Pending, 2: Paid, 3: Late');
            $table->timestamps();

            //relationships
            $table->foreign('tenant_id')->references('id')->on('tenants')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreign('room_id')->references('id')->on('rooms')->cascadeOnDelete()->cascadeOnUpdate();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tenants');
        Schema::dropIfExists('rent_payments');
    }
};
