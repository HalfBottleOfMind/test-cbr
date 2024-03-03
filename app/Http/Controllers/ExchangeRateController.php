<?php

namespace App\Http\Controllers;

use App\Models\ExchangeRate;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;

class ExchangeRateController extends Controller
{
    public function index(): Collection
    {
        return ExchangeRate::query()->onlyLast()->get();
    }

    public function period(Request $request): Collection
    {
        return ExchangeRate::query()
            ->where('date', '>=', $request->get('from'))
            ->where('date', '<=', $request->get('to'))->get();
    }
}
