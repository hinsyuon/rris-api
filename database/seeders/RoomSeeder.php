<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Room;

class RoomSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $data = [
            ['id' => 1, 'room_number' => '101', 'room_type_id' => 1, 'price_per_month' => 50.00, 'status' => Room::$AVAILABLE, 'description' => 'Cozy single room with a great view.'],
            ['id' => 2, 'room_number' => '102', 'room_type_id' => 2, 'price_per_month' => 80.00, 'status' => Room::$AVAILABLE, 'description' => 'Spacious double room perfect for couples.'],
            ['id' => 3, 'room_number' => '201', 'room_type_id' => 3, 'price_per_month' => 150.00, 'status' => Room::$AVAILABLE, 'description' => 'Luxurious suite with modern amenities.'],
        ];

        foreach ($data as $item) {
            $room = new Room($item);
            $room->id = $item['id'];
            $room->save();
        }
    }
}
