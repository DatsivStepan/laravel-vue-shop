<?php

namespace Modules\Notifications\Services\Templates;

use Illuminate\Support\Arr;
use Modules\Notifications\Entities\NotificationTemplate;
use Modules\Notifications\Services\Tags\Tags;

abstract class Template
{
    /** @var NotificationTemplate $template */
    protected $template;
    /** @var Tags */
    public $tags;

    public function __construct(array $tags)
    {
        $tagsDict = $this->prepareTags($tags);
        $this->tags = app()->make(Tags::class, ['tags' => $tagsDict]);
        $this->loadTemplate();
    }

    abstract public function tagsList(): array;

    abstract public function getDBKey(): ?string;

    abstract public function getLangKey(): ?string;

    protected function prepareTags(array $tags): array
    {
        $tagsDict = array_fill_keys($this->tagsList(), null);
        return array_merge($tagsDict, Arr::only($tags, $this->tagsList()));
    }

    protected function loadTemplate()
    {
        $this->template = NotificationTemplate::query()
            ->firstOrNew($this->getParamsForSearchTemplate(), $this->getParamsForDefaultTemplate());
        $this->parseBody();
    }

    protected function getParamsForSearchTemplate(): array
    {
        return ['key' => $this->getDBKey()];
    }

    protected function getParamsForDefaultTemplate(): array
    {
        return [
            'body' => __($this->getLangKey() . '.body'),
            'status' => boolval(__($this->getLangKey() . '.status')),
            'subject' => __($this->getLangKey() . '.subject'),
        ];
    }

    protected function parseBody()
    {
        $this->template->body = str_replace($this->tags->keys(), $this->tags->values(), $this->template->body);
    }

    public function getBody()
    {
        return $this->template->body;
    }

    public function getEmailForSendCopy(): ?string
    {
        return $this->template->send_copy_to;
    }

    public function getStatus(): bool
    {
        return $this->template->status;
    }

    public function getSubject(): string
    {
        return $this->template->subject ?? '';
    }
}
