<?php

namespace Modules\Notifications\Tests\Feature;

use Modules\Notifications\Entities\NotificationTemplate;
use Modules\Notifications\Http\Resources\NotificationTemplateCollection;
use Modules\Notifications\Http\Resources\NotificationTemplateResource;
use Modules\Notifications\Services\TemplateFactory;
use Modules\Notifications\Tests\Templates\MockFactory;
use Tests\TestCase;

class NotificationTemplateControllerTest extends TestCase
{

    protected $tenancy = true;
    protected $createUser = true;

    public function setUp(): void
    {
        parent::setUp();
        $this->instance(TemplateFactory::class, new MockFactory());
    }

    public function testUserCanGetAllTemplates()
    {
        $templates = factory(NotificationTemplate::class, 2)->create([]);
        $resource = (new NotificationTemplateCollection($templates))->response();

        $this->apiAs('GET', route('notifications.admin.templates.index'))
            ->assertJsonFragment($resource->getData(true));
    }

    public function testUserCanGetSeparateTemplate()
    {
        $template = factory(NotificationTemplate::class)->create();
        $resource = (new NotificationTemplateResource($template))->response();

        $this->apiAs('GET', route('notifications.admin.templates.show', ['notificationTemplate' => $template]))
            ->assertJsonFragment($resource->getData(true));
    }

    public function testUserCanUpdateTemplate()
    {
        $template = factory(NotificationTemplate::class)->create();

        $payload = [
            'subject' => 'test subject',
            'body' => 'test body',
            'status' => false
        ];

        $this->apiAs('PUT', route('notifications.admin.templates.update', ['notificationTemplate' => $template->id]),
            $payload)
            ->assertJsonFragment($payload);
    }
}
