<?php

namespace App\Jobs;

use App\Models\ExchangeRate;
use App\Services\Cbr;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Throwable;

class SyncExchangeRates implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 5;

    public int $backoff = 30;

    public function handle(): void
    {
        $this->log('Sync started. Attempt ' . $this->job->attempts());

        $cbrDto = Cbr::getExchangeRates();

        foreach($cbrDto->rates as $rate) {
            ExchangeRate::query()->updateOrCreate(
                [
                    'date' => $cbrDto->date,
                    'id' => $rate->id,
                ],
                [
                    'num_code' => $rate->numCode,
                    'char_code' => $rate->charCode,
                    'nominal' => $rate->nominal,
                    'name' => $rate->name,
                    'value' => $rate->value,
                    'v_unit_rate' => $rate->vUnitRate,
                ]
            );
        }

        $this->log('Sync finished');
    }

    public function failed(Throwable $exception): void
    {
        $this->log($exception->getMessage());
    }

    private function log(string $message): void
    {
        Log::channel('exchange-rates-sync')->info($message);
    }
}
