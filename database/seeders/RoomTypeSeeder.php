<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use \App\Models\RoomType;

class RoomTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $data = [
            ['id' => 1 , 'name' => 'Single', 'description' => 'A room assigned to one person. May have one or more beds.'],
            ['id' => 2 , 'name' => 'Double', 'description' => 'A room assigned to two people. May have one or more beds.'],
            ['id' => 3 , 'name' => 'Suite', 'description' => 'A parlour or living room connected with to one or more bedrooms.'],
        ];

        foreach ($data as $item) {
            $roomType = new RoomType($item);
            $roomType->id = $item['id'];
            $roomType->save();
        }

    }
}
