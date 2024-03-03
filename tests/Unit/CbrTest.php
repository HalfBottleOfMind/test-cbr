<?php

namespace Tests\Unit;

use App\Dto\ExchangeRateDto;
use App\Services\Cbr;
use ErrorException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Http;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class CbrTest extends TestCase
{
    #[Test]
    public function exchangeRatesParsingIsCorrect(): void
    {
        //dd($this->getValidResponse());
        Http::fake(['*' => Http::response($this->getValidResponse(), Response::HTTP_OK, ['content-type' => 'application/xml; charset=windows-1251'])]);

        $result = Cbr::getExchangeRates();

        $this->assertEquals('02.03.2024', $result->date);
        $this->assertEquals($result->rates[0], $this->getValidResponseCurrencies()[0]);
        $this->assertEquals($result->rates[1], $this->getValidResponseCurrencies()[1]);
    }

    #[Test]
    public function brokenXmlInResponse(): void
    {
        Http::fake(['*' => Http::response($this->getInvalidResponse(), Response::HTTP_OK)]);

        $this->expectException(ErrorException::class);
        Cbr::getExchangeRates();
    }

    #[Test]
    public function notSuccesfulResponseStatus(): void
    {
        Http::fake(['*' => Http::response($this->getInvalidResponse(), Response::HTTP_I_AM_A_TEAPOT)]);

        $this->expectException(RequestException::class);
        Cbr::getExchangeRates();
    }

    private function getValidResponse(): string
    {
        return
            mb_convert_encoding('<?xml version="1.0" encoding="windows-1251"?>
            <ValCurs Date="02.03.2024" name="Foreign Currency Market">
                <Valute ID="R01010">
                    <NumCode>036</NumCode>
                    <CharCode>AUD</CharCode>
                    <Nominal>1</Nominal>
                    <Name>Австралийский доллар</Name>
                    <Value>59,4582</Value>
                    <VunitRate>59,4582</VunitRate>
                </Valute>
                <Valute ID="R01020A">
                    <NumCode>944</NumCode>
                    <CharCode>AZN</CharCode>
                    <Nominal>1</Nominal>
                    <Name>Азербайджанский манат</Name>
                    <Value>53,7256</Value>
                    <VunitRate>53,7256</VunitRate>
                </Valute>
            </ValCurs>', 'windows-1251', 'utf-8');
    }

    private function getInvalidResponse(): string
    {
        return
            mb_convert_encoding('<?xml version="1.0" encoding="windows-1251"?>
            <ValCurs Date="02.03.2024" name="Foreign Currency Market">
                <Valute ID="R01010>
                    <NumCode>036</NumCode>
                    <CharCode>AUD</CharCode>
                    <Nominal>1</Nominal>
                    <Name>Австралийский доллар</Name>
                    <Value>59,4582</Value>
                    <VunitRate>59,4582</VunitRate>
                </Valute>
           </ValCurs>', 'windows-1251', 'utf-8');
    }

    private function getValidResponseCurrencies(): array
    {
        return [
            new ExchangeRateDto(
                'R01010',
                '036',
                'AUD',
                1,
                'Австралийский доллар',
                59.4582,
                59.4582,
            ),
            new ExchangeRateDto(
                'R01020A',
                '944',
                'AZN',
                1,
                'Азербайджанский манат',
                53.7256,
                53.7256,
            ),
        ];

    }
}
