<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use \App\Models\Tenant;

class TenantSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $data = [
            ['id' => 1, 'first_name' => 'John', 'last_name' => 'Doe', 'gender' => Tenant::MALE, 'email' => 'john.doe@example.com', 'phone_number' => '123-456-7890', 'address' => '123 Main St', 'joined_at' => now()],
            ['id' => 2, 'first_name' => 'Jane', 'last_name' => 'Smith', 'gender' => Tenant::FEMALE, 'email' => 'jane.smith@example.com', 'phone_number' => '987-654-3210', 'address' => '456 Elm St', 'joined_at' => now()],
            ['id' => 3, 'first_name' => 'Alex', 'last_name' => 'Johnson', 'gender' => Tenant::OTHER, 'email' => 'alex.johnson@example.com', 'phone_number' => '555-555-5555', 'address' => '789 Oak St', 'joined_at' => now()],
        ];

        foreach ($data as $item) {
            $tenant = new Tenant($item);
            $tenant->id = $item['id'];
            $tenant->save();
        }
    }
}
