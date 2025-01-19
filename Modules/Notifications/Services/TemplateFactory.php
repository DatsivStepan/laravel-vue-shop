<?php

namespace Modules\Notifications\Services;

use Modules\Notifications\Services\Templates\Template;

interface TemplateFactory
{
    public function make($key, array $tags): Template;
}
