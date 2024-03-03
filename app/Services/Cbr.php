<?php

namespace App\Services;

use App\Dto\CbrDto;
use App\Dto\ExchangeRateDto;
use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class Cbr
{
    public static function getExchangeRates(): CbrDto
    {
        $response = Http::get(config('cbr.currencies_url'));
        if (!$response->successful()) {
            Log::error('CBR responded with not 2** code', ['code' => $response->status()]);
            $response->throw();
        }
        $body = $response->body();
        $body = mb_convert_encoding($response->body(), 'utf-8', 'windows-1251');
        $body = str_replace('windows-1251', 'utf-8', $body);

        $parsed = simplexml_load_string($response->body());
        if ($parsed === false) {
            Log::error('XML parsing error');
            throw(new Exception('Xml parsing error'));
        }

        $dto = new CbrDto();
        $dto->date = (string)$parsed['Date'];

        foreach($parsed->Valute as $rate) {
            $dto->rates[] = new ExchangeRateDto(
                (string)$rate['ID'],
                (string)$rate->NumCode,
                (string)$rate->CharCode,
                (int)$rate->Nominal,
                (string)$rate->Name,
                (float)str_replace(',', '.', $rate->Value),
                (float)str_replace(',', '.', $rate->VunitRate),
            );
        }

        return $dto;
    }
}
