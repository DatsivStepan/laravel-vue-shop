<?php

namespace App\Models;

/*
 **
 * @property Collection parents
 */
class TestRole extends User
{
    protected $table = 'users';

    public function getMorphClass()
    {
        return 'App\Models\User';
    }
}
