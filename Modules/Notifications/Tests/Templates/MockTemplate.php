<?php

namespace Modules\Notifications\Tests\Templates;

use Modules\Notifications\Services\Templates\Template;

class MockTemplate extends Template
{
    public function tagsList(): array
    {
        return ['mock'];
    }

    public function getDBKey(): ?string
    {
        return 'mock';
    }

    public function getLangKey(): ?string
    {
        return 'mock';
    }
}
