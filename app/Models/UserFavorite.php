<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserFavorite extends Model
{
    use HasFactory;

    protected $table = 'usersfavorites';
    public $hidden = 'barber_id';
    public  $timestamps = false;
}
