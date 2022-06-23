<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BarberPhotos extends Model
{
    use HasFactory;
    protected $table = 'barberphotos';
    public $hidden = ['barber_id'];
    public  $timestamps = false;


}
