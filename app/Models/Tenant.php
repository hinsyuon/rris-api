<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tenant extends Model
{
    //
    public const MALE = 1;
    public const FEMALE = 2;
    public const OTHER = 3;
    public const PENDING = 0;
    public const PAID = 1;
    public const LATE = 2;
    
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
