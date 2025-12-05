<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    // constant block
    public const IS_ACTIVE = 1;
    public const IS_DEACTIVATE = 0;
    public const IS_FIRST_TIME = 1;
    public const IS_NOT_FIRST_TIME = 0;
    public const ALLOW_NOTIFICATION = 1;
    public const MUTE_NOTIFICATION = 0;
    public const DEFAULT_AVATAR  = "avatars/no_photo.jpg";
    public const FAILED_ATTEMPT = 5;
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'name_updated_at',
        'phone',
        'email',
        'avatar',
        'password'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // Define relationship with Role
    public function roles()
    {
        return $this->belongsToMany(Role::class, 'user_role', 'user_id', 'role_id');
    }

    public function hasRole($roleId): bool
    {
        return $this->roles()->where('role_id', $roleId)->exists();
    }
    public function isSuperAdmin(): bool
    {
        // Assuming role ID 1 is for Super Admin
        return $this->hasRole(1);
    }
    public function isAdmin(): bool
    {
        // Assuming role ID 2 is for Admin
        return $this->hasRole(2);
    }
    public function isUser(): bool
    {
        // Assuming role ID 3 is for Regular User
        return $this->hasRole(3);
    }
    public function permissions()
    {
        return $this->roles()->with('permissions')->get()->pluck('permissions')->flatten()->unique('id');
    }
    public function hasPermission($permissionName): bool
    {
        return $this->permissions()->where('name', $permissionName)->isNotEmpty();
    }
    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }
    public function unreadNotifications()
    {
        return $this->hasMany(Notification::class)->where('read_status', Notification::STATUS_UNREAD);
    }
    public function markAllNotificationsAsRead()
    {
        return $this->unreadNotifications()->update(['read_status' => Notification::STATUS_READ]);
    }
  
}
