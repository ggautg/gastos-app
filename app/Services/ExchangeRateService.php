<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class ExchangeRateService
{
    public function getUsdToGsRate(): float
    {
        return Cache::remember('usd_to_gs_rate', now()->addHours(6), function () {
            $response = Http::timeout(5)->get('https://open.er-api.com/v6/latest/USD');

            if ($response->successful()) {
                return $response->json('rates.PYG');
            }

            return 7800; // valor de respaldo si la API falla
        });
    }
}