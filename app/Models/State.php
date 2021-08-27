<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;

class State extends Model
{
    use HasFactory;
    use SoftDeletes;

    public $timestamps = false;

    /**
     * Get the suppliers associated with the state.
     */
    public function state()
    {
        return $this->hasMany(Supplier::class);
    }

}
