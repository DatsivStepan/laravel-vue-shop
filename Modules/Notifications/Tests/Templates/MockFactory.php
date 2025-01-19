<?php

namespace Modules\Notifications\Tests\Templates;

use Modules\Notifications\Services\TemplateFactory;
use Modules\Notifications\Services\Templates\Template;

class MockFactory implements TemplateFactory
{

    public function make($key, array $tags): Template
    {
        return new MockTemplate($tags);
    }
}
