<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    public static $AVAILABLE = 1;
    public static $BOOKED = 2;

    // Define the fillable fields for mass assignment
    protected $fillable = [
        'room_number',
        'room_type_id',
        'price_per_month',
        'status',
        'description'
    ];

    // Define relationship with RoomType
    public function room_type()
    {
        return $this->belongsTo(RoomType::class, 'room_type_id');
    }
    // Define relationship with RentPayment
    public function tenants()
    {
        return $this->belongsToMany(Tenant::class, 'rent_payments', 'room_id', 'tenant_id')
                    ->withPivot(
                        'amount_paid', 
                        'payment_date', 
                        'payment_status'
                    )->withTimestamps();       
    }
}