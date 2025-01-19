<?php

namespace Modules\Notifications\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Notifications\Services\TemplateFactory;
use Modules\Notifications\Services\Templates\Template;

/** @mixin \Modules\Notifications\Entities\NotificationTemplate */
class NotificationTemplateResource extends JsonResource
{
    /** @var Template */
    private $template;

    public function __construct($resource)
    {
        parent::__construct($resource);

        $this->template = app(TemplateFactory::class)->make($this->key, []);
    }

    /**
     * @param \Illuminate\Http\Request $request
     *
     * @return array
     */
    public function toArray($request)
    {
        return [
            'body' => $this->body,
            'key' => $this->key,
            'subject' => $this->subject,
            'send_copy_to' => $this->send_copy_to,
            'status' => $this->status,
            'id' => $this->id,
            'tags' => $this->template->tags->keys(),
            'created_at' => $this->created_at ? Carbon::createFromFormat('Y-m-d H:i:s', $this->created_at)
                ->format(__('langconfig.iso8601')) : '',
            'updated_at' => $this->updated_at ? Carbon::createFromFormat('Y-m-d H:i:s', $this->updated_at)
                ->format(__('langconfig.iso8601')) : '',
        ];
    }
}
