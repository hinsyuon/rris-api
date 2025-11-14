<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RoomType extends Model
{
    public $timestamps = false; // Disable timestamps if not needed

    // Define the fillable fields for mass assignment
    protected $fillable = [
        'name',
        'description'
    ];
}
