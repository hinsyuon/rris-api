<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Room;

return new class extends Migration
{
    /**
     * Run the migrations.
     */

    public function up(): void
    {
        // Room Types Table
        Schema::create('room_types', function (Blueprint $table) {
            $table->unsignedTinyInteger('id', true)->primary();
            $table->string('name')->unique();
            $table->text('description')->nullable();
        });
        // Rooms Table
        Schema::create('rooms', function (Blueprint $table) {
            $table->id();
            $table->string('room_number')->unique();
            $table->unsignedTinyInteger('room_type_id')->comment('1: Single, 2: Double, 3: Suite');
            $table->decimal('price_per_month', 10, 2)->default(0.00);
            $table->unsignedTinyInteger('status')->default(Room::$AVAILABLE)->comment('1: Available, 2: Booked');
            $table->string('description')->nullable();
            $table->timestamps();
            //relationships
            $table->foreign('room_type_id')->references('id')->on('room_types')->cascadeOnDelete()->cascadeOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rooms');
        Schema::dropIfExists('room_types');
    }
};
