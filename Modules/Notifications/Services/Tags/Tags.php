<?php

namespace Modules\Notifications\Services\Tags;

abstract class Tags
{
    /** @var array $tags */
    protected $tags;

    public function __construct(array $tags)
    {
        $this->tags = $tags;
    }

    abstract protected function separatorBeforeTag(): string;

    abstract protected function separatorAfterTag(): string;

    public function keys(): array
    {
        return array_map(function ($item) {
            return $this->separatorBeforeTag() . $item . $this->separatorAfterTag();
        }, array_keys($this->tags));
    }

    public function values(): array
    {
        return array_values($this->tags);
    }
}
