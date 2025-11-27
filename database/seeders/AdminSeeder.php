<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Carbon\Carbon;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;
use PragmaRX\Google2FA\Google2FA;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            [
                'name' => 'Super Admin',
                'phone' => '012345676',
                'email' => config('app.env') !== 'local' ? config('app.super_admin_email') : 'superadmin@rris.com',
                'password' => config('app.env') !== 'local' ? config('app.super_admin_password') : Hash::make('11223344'),
                'roles' => [Role::SUPER_ADMIN]
            ],
            [
                'name' => 'Admin',
                'phone' => '012345677',
                'email' => config('app.env') !== 'local' ? config('app.admin_email') : 'admin@rris.com',
                'password' => config('app.env') !== 'local' ? config('app.admin_password') : Hash::make('11223344'),
                'roles' => [Role::ADMIN]
            ],
            [
                'name' => 'Regular User',
                'phone' => '012345678',
                'email' => config('app.env') !== 'local' ? config('app.regular_user_email') : 'regularuser@rris.com',
                'password' => config('app.env') !== 'local' ? config('app.regular_user_password') : Hash::make('11223344'),
                'roles' => [Role::REGULAR_USER]
            ],
        ];

        $google2fa = new Google2FA();

        foreach ($users as $data) {
            $user = new User([
                'name' => $data['name'],
                'phone' => $data['phone'],
                'email' => $data['email'],
                'password' => $data['password'],
                'avatar' => User::DEFAULT_AVATAR,
            ]);

            $user->is_active = User::IS_ACTIVE;
            $user->is_first_time = User::IS_NOT_FIRST_TIME;
            $user->email_verified_at = Carbon::now();
            if (config('app.env') === 'local') {
                $user->google2fa_secret = $google2fa->generateSecretKey();
            }
            $user->save();
            $user->roles()->sync($data['roles']);
        }
    }
}