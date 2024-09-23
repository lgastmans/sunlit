<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FreightZone extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'rate_per_kg', 'properties'];

    protected $casts = [
        'properties' => 'array',
    ];

    public function setPropertiesAttribute($value)
    {
        $properties = [];

        foreach ($value as $array_item) {
            if (! is_null($array_item['key'])) {
                $properties[] = $array_item;
            }
        }

        $this->attributes['properties'] = json_encode($properties);
    }
}
