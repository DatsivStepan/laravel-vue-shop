<?php

namespace Modules\Notifications\Services\Attachments;

class Attachment
{
    protected $path;

    protected $options = [];

    public function __construct(string $path, array $options = []) {
        $this->path = $path;
        $this->options = $options;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function getOptions(): array
    {
        return $this->options;
    }
}
