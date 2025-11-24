<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tenant extends Model
{
    //
    public static $MALE = 1;
    public static $FEMALE = 2;
    public static $OTHER = 3;
    // Define the fillable fields for mass assignment
    protected $fillable = [
        'first_name',
        'last_name',
        'gender',
        'age',
        'email',
        'phone_number',
        'address'
    ];
    // Define relationship with RentPayment
    public function rooms()
    {
        return $this->belongsToMany(Room::class, 'rent_payments', 'tenant_id', 'room_id')
                    ->withPivot(
                        'amount_paid', 
                        'payment_date', 
                        'payment_status'
                    )->withTimestamps();       
        }

}
