<?php

namespace App\Dto;

class CbrDto
{
    public string $date;

    /** @var ExchangeRateDto[] */
    public array $rates;
}
