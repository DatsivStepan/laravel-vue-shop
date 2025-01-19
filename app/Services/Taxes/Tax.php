<?php

namespace App\Services\Taxes;

use App\Models\User;

interface Tax
{
    public function calculate(User $user): float;
}
