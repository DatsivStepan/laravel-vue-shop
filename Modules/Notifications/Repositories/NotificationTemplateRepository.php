<?php

namespace Modules\Notifications\Repositories;

use Modules\Notifications\Entities\NotificationTemplate;
use Spatie\QueryBuilder\QueryBuilder;

class NotificationTemplateRepository
{
    public function paginate(int $perPage)
    {
        return QueryBuilder::for(NotificationTemplate::class)
            ->allowedFilters([
                'subject'
            ])
            ->allowedSorts(['subject', 'status'])
            ->paginate($perPage);
    }

    public function find($id)
    {
        return NotificationTemplate::find($id);
    }

    public function findOrFail($id)
    {
        return NotificationTemplate::findOrFail($id);
    }

    public function update($notification, $payload)
    {
        if (!($notification instanceof NotificationTemplate)) {
            $notification = $this->findOrFail($notification);
        }

        $notification->update($payload);

        return $notification;
    }
}
