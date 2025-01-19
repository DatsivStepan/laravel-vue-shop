<?php

namespace Modules\Notifications\Services\Tags;

class CurlyBracesTags extends Tags
{
    protected function separatorBeforeTag(): string
    {
        return '{{';
    }

    protected function separatorAfterTag(): string
    {
        return '}}';
    }
}
