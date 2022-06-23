<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BarberTestimonial extends Model
{
    use HasFactory;
    protected $table = 'barbertestimonials';
    public $hidden = ['barber_id'];
    public  $timestamps = false;
}
