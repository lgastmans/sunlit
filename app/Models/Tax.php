<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Tax extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = ['name', 'amount'];

    /**
     * Get the products associated with the tax.
     */
    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }
}
