<?php

namespace App\Dto;

class ExchangeRateDto
{
    public function __construct(
        public string $id,
        public string $numCode,
        public string $charCode,
        public int $nominal,
        public string $name,
        public float $value,
        public float $vUnitRate,
    ) { }
}
