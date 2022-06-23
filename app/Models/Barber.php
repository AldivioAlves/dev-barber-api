<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Barber extends Model
{
    use HasFactory;
    public  $timestamps = false;

    public function  photos(){
        return  $this->hasMany(BarberPhotos::class);
    }

    public function testimonials(){
        return $this->hasMany(BarberTestimonial::class);
    }

    public function services(){
        return $this->hasMany(BarberServices::class);
    }

    public function availabilities(){
        return $this->hasMany(BarberAvailability::class);
    }

    public function appointments(){
        return $this->hasMany(UserAppointment::class);
    }
}
