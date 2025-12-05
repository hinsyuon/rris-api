<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    //
    public const TYPE_ROOM_SUBMITTED = 1;
    public const TYPE_ROOM_PUBLISHED = 2;
    public const TYPE_NEW_LOGIN = 3;
    public const TYPE_BOOKING_REQUEST = 4;
    public const TYPE_PAYMENT = 5;
    public const TYPE_BOOKING_APPROVED = 6;
    public const TYPE_BOOKING_REJECTED = 7;
    public const TYPE_TENANT_ADDED = 8;
    public const TYPE_TENANT_REMOVED = 9;
    public const TYPE_ROOM_UNAVAILABLE = 10;
    public const TYPE_PASSWORD_CHANGED = 11;
    public const TYPE_PROFILE_UPDATED = 12;
    public const TYPE_OTHERS = 13;

    public const STATUS_UNREAD = 1;
    public const STATUS_READ = 2;  

    public $timestamps = true;

    protected $fillable = [
        'user_id',
        'message',
        'type',
        'read_status',
    ];

    // Define relationships, accessors, or other model methods as needed
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    // Scope for unread notifications
    public function scopeUnread($query)
    {
        return $query->where('read_status', self::STATUS_UNREAD);
    }

    // Scope for read notifications
    public function scopeRead($query)
    {
        return $query->where('read_status', self::STATUS_READ);
    }

    // Mark as read
    public function markAsRead(): void
    {
        $this->read_status = self::STATUS_READ;
        $this->save();
    }

    // Mark as unread
    public function markAsUnread(): void
    {
        $this->read_status = self::STATUS_UNREAD;
        $this->save();
    }

    // Send notification to a user
    public static function sendToUser(int $userId, string $message, int $type = self::TYPE_OTHERS): self
    {
        return self::create([
            'user_id' => $userId,
            'message' => $message,
            'type'    => $type,
            'read_status' => self::STATUS_UNREAD,
        ]);
    }

    // Helper to get human-readable type
    public function getTypeName(): string
    {
        return match ($this->type) {
            self::TYPE_ROOM_SUBMITTED => 'Room Submitted',
            self::TYPE_ROOM_PUBLISHED => 'Room Published',
            self::TYPE_NEW_LOGIN => 'New Login',
            self::TYPE_BOOKING_REQUEST => 'Booking Request',
            self::TYPE_PAYMENT => 'Payment',
            self::TYPE_BOOKING_APPROVED => 'Booking Approved',
            self::TYPE_BOOKING_REJECTED => 'Booking Rejected',
            self::TYPE_TENANT_ADDED => 'Tenant Added',
            self::TYPE_TENANT_REMOVED => 'Tenant Removed',
            self::TYPE_ROOM_UNAVAILABLE => 'Room Unavailable',
            self::TYPE_PASSWORD_CHANGED => 'Password Changed',
            self::TYPE_PROFILE_UPDATED => 'Profile Updated',
            default => 'Others',
        };
    }


}
