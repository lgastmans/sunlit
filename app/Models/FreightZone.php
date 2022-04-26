<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FreightZone extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'rate_per_kg'];
    
}
