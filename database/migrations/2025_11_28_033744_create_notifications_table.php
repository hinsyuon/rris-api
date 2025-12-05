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
            $table->unsignedBigInteger('user_id');
            $table->text('message')->nullable();
            $table->unsignedSmallInteger('type')
                  ->comment('1 for room submitted, 2 for room published, 3 for new_login, 4 for booking_request, 5 for payment, 6 for booking_approved, 7 for booking_rejected, 8 for tenant_added, 9 for tenant_removed, 10 for room_unavailable, 11 for password_changed, 12 for profile_updated, 13 for others');
            $table->unsignedSmallInteger('read_status')->default(1)->comment('1 for  unread, 2 for read');
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->cascadeOnUpdate()->cascadeOnDelete();
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
