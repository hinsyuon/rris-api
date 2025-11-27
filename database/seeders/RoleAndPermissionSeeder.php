<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Support\Facades\DB;

class RoleAndPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Permissions
        $permissions = [
            ['id'=>1,'name'=>'manage_tenants'],
            ['id'=>2,'name'=>'manage_rooms'],
            ['id'=>3,'name'=>'manage_payments'],
            ['id'=>4,'name'=>'manage_users'],
            ['id'=>5,'name'=>'view_reports'],
            ['id'=>6,'name'=>'system_settings'],
        ];
        foreach($permissions as $permission) {
            $permission = new Permission($permission);
            $permission->save();
        }   

  
        // 2. Assign permissions
        $allPermissions = DB::table('permissions')->pluck('id')->toArray();
        foreach($allPermissions as $pid) {
            DB::table('role_permission')->insert([
                'role_id'=>Role::SUPER_ADMIN,
                'permission_id'=>$pid
            ]);
        }

        // Admin gets only operational permissions
        $adminPermissions = DB::table('permissions')
            ->whereIn('name',['manage_tenants','manage_rooms','manage_payments','view_reports'])
            ->pluck('id')->toArray();
        foreach($adminPermissions as $pid) {
            DB::table('role_permission')->insert([
                'role_id'=>Role::ADMIN,
                'permission_id'=>$pid
            ]);
        }

        // Regular User gets read-only access (view_reports)
        $regularPermissions = DB::table('permissions')->where('name','view_reports')->pluck('id')->toArray();
        foreach($regularPermissions as $pid) {
            DB::table('role_permission')->insert([
                'role_id'=>Role::REGULAR_USER,
                'permission_id'=>$pid
            ]);
        }
 
    }
}
