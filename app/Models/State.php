<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class State extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = ['freight_zone_id', 'name', 'code', 'abbreviation'];

    protected $with = ['freight_zone'];

    public $timestamps = false;

    /**
     * Get the freight zones associated with the state.
     */
    public function freight_zone(): BelongsTo
    {
        return $this->belongsTo(FreightZone::class);
    }
}
