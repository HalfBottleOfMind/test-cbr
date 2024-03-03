<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExchangeRate extends Model
{
    use HasFactory;

    public $guarded = [];

    public $timestamps = false;

    public $incrementing = false;

    public static function scopeOnlyLast(Builder $query): void
    {
        $query
            ->fromRaw('(select exchange_rates.*, row_number() over(partition by id order by date desc) as rn from exchange_rates)')
            ->where('rn', '=', 1);
    }
}
