<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    //
    public const SUPER_ADMIN  = 1;
    public const ADMIN = 2;
    public const REGULAR_USER = 3;
     
    public $timestamps = false;
    // Define the fillable fields for mass assignment
    protected $fillable = [
        'name'
    ];

    // Define relationship with Permission
    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'role_permission', 'role_id', 'permission_id');
    }

    // Define relationship with User
    public function users()
    {
        return $this->belongsToMany(User::class, 'user_role', 'role_id', 'user_id');
    }
}