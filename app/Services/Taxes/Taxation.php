<?php

namespace App\Services\Taxes;

interface Taxation
{
    public function getTaxationClass(): string;

    public function getFields(): array;
}
