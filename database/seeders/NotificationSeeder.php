<?php

namespace Database\Seeders;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;

class NotificationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('notifications')->insert([
            [
                'user_id' => 1,
                'message' => 'Welcome to the system!',
                'type' => 13,
                'read_status' => 1,
            ],
            [
                'user_id' => 2,
                'message' => 'Your room has been published.',
                'type' => 2,
                'read_status' => 1,
            ],
            [
                'user_id' => 3,
                'message' => 'New login detected from a new device.',
                'type' => 3,
                'read_status' => 1,
            ],
            [
                'user_id' => 4,
                'message' => 'Your booking request has been approved.',
                'type' => 6,
                'read_status' => 1,
            ],
            [
                'user_id' => 5,
                'message' => 'Your password has been changed successfully.',
                'type' => 11,
                'read_status' => 1,
            ],
            [
                'user_id' => 1,
                'message' => 'A new tenant has been added to your account.',
                'type' => 8,
                'read_status' => 1,
            ],
            [
                'user_id' => 2,
                'message' => 'Your room is now unavailable for booking.',
                'type' => 10,
                'read_status' => 1,
            ],
            [
                'user_id' => 3,
                'message' => 'Your profile has been updated successfully.',
                'type' => 12,
                'read_status' => 1,
            ],
            [
                'user_id' => 4,
                'message' => 'Payment received for your booking.',
                'type' => 5,
                'read_status' => 1,
            ],
            [
                'user_id' => 5,
                'message' => 'Your booking request has been rejected.',
                'type' => 7,
                'read_status' => 1,
            ],
            [
                'user_id' => 1,
                'message' => 'A room has been submitted for your review.',
                'type' => 1,
                'read_status' => 1,
            ],
            [
                'user_id' => 2,
                'message' => 'A tenant has been removed from your account.',
                'type' => 9,
                'read_status' => 1,
            ],
            [
                'user_id' => 3,
                'message' => 'System maintenance scheduled for tonight.',
                'type' => 13,
                'read_status' => 1,
            ],
            [
                'user_id' => 4,
                'message' => 'New features have been added to your dashboard.',
                'type' => 13,
                'read_status' => 1,
            ],
            [
                'user_id' => 5,
                'message' => 'Your booking has been confirmed.',
                'type' => 6,
                'read_status' => 1,
            ],
            [
                'user_id' => 1,
                'message' => 'Reminder: Your rent payment is due soon.',
                'type' => 5,
                'read_status' => 1,
            ],
            [
                'user_id' => 2,
                'message' => 'Your room listing has been updated.',
                'type' => 2,
                'read_status' => 1,
            ],
            [
                'user_id' => 3,
                'message' => 'Unusual activity detected on your account.',
                'type' => 3,
                'read_status' => 1,
            ],
            [
                'user_id' => 4,
                'message' => 'Thank you for being a valued tenant!',
                'type' => 13,
                'read_status' => 1,
            ],
            [
                'user_id' => 5,
                'message' => 'Your booking request is being processed.',
                'type' => 4,
                'read_status' => 1,
            ],
            [
                'user_id' => 1,
                'message' => 'System update completed successfully.',
                'type' => 13,
                'read_status' => 1,
            ],
            [
                'user_id' => 2,
                'message' => 'New tenant application received.',
                'type' => 4,
                'read_status' => 1,
            ],
            [
                'user_id' => 3,
                'message' => 'Your room has been marked as unavailable.',
                'type' => 10,
                'read_status' => 1,
            ],
            [
                'user_id' => 4,
                'message' => 'Password reset successful.',
                'type' => 11,
                'read_status' => 1,
            ],
            [
                'user_id' => 5,
                'message' => 'Profile information updated.',
                'type' => 12,
                'read_status' => 1,
            ],
            [
                'user_id' => 1,
                'message' => 'New booking request submitted.',
                'type' => 4,
                'read_status' => 1,
            ],
            [
                'user_id' => 2,
                'message' => 'Payment processed successfully.',
                'type' => 5,
                'read_status' => 1,
            ],
            [
                'user_id' => 3,
                'message' => 'Booking approved. Enjoy your stay!',
                'type' => 6,
                'read_status' => 1, 

            ],
            [
                'user_id' => 4,
                'message' => 'Booking rejected. Please contact support.',
                'type' => 7,
                'read_status' => 1,
            ],
            [
                'user_id' => 5,
                'message' => 'Welcome aboard! Your account is now active.',
                'type' => 13,
                'read_status' => 1,
            ]

        ]);
    }
}
