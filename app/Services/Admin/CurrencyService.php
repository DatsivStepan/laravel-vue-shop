<?php

namespace App\Services\Admin;

use Illuminate\Database\Eloquent\Collection;

interface CurrencyService
{
    public function getCurrenciesWithDefault(): Collection;
}
