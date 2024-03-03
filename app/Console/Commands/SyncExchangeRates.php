<?php

namespace App\Console\Commands;

use App\Jobs\SyncExchangeRates as SyncCurrenciesJob;
use Illuminate\Console\Command;

class SyncExchangeRates extends Command
{
    protected $signature = 'sync-currencies';

    public function handle(): void
    {
        SyncCurrenciesJob::dispatchSync();
    }
}
