<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    //
    public const MANAGE_TENANTS = 1;
    public const MANAGE_ROOMS = 2;
    public const MANAGE_PAYMENTS = 3;
    public const MANAGE_USERS = 4;
    public const VIEW_REPORTS = 5;
    public const SYSTEM_SETTINGS = 6;

    public $timestamps = false;
    // Define the fillable fields for mass assignment
    protected $fillable = [
        'name'
    ];

    // Define relationship with Role
    public function roles()
    {
        return $this->belongsToMany(Role::class, 'role_permission', 'permission_id', 'role_id');
    }

}
