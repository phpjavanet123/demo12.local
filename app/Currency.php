<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Currency extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'name', 'code', 'symbol',
    ];

    /**
     * Get the rate record associated with the currency.
     */
    public function rates()
    {
        return $this->hasMany(ExchangeRate::class);
    }
}
