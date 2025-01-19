<?php

namespace Modules\Notifications\Http\Resources;

use App\Helpers\GridDataHelper;
use Illuminate\Http\Resources\Json\ResourceCollection;

/** @see \Modules\Notifications\Entities\NotificationTemplate */
class NotificationTemplateCollection extends ResourceCollection
{
    public function toArray($request)
    {
        $data = $this->getData()->toArray();
        return [
            'data' => [
                'headerItems' => $this->getHeaderItems(),
                'rowsItems' => $this->getRowsItems($data),
                'filters' => $this->getFilters(),
                'options' => [],
            ],
        ];
    }

    public function getData()
    {
        return $this->collection->map(function ($collection) {
            return (new NotificationTemplateResource($collection))->toArray(request());
        });
    }


    public function getHeaderItems()
    {
        return [
            'subject' => __('notifications.form.subject'),
            'status' => __('notifications.form.status'),
            'actions' => ''
        ];
    }

    public function getRowsItems(array $data)
    {
        return collect($data)->map(function ($collection) {
            return [
                'subject' => [
                    'value' => $collection['subject'],
                ],
                'status' => [
                    'value' => $collection['status'] ? __('system.active') : __('system.inactive'),
                ],
                'actions' => $this->getActions($collection),
            ];
        });
    }

    public function getActions($record)
    {
        $actions = [];

        $actions[] = GridDataHelper::generateEditButton(
            'admin-notifications-edit',
            $record['id'],
            __('system.edit')
        );

        $actions[] = GridDataHelper::generateActionApi(
            $record['status'] ? __('system.inactive') : __('system.active'),
            route('notifications.admin.templates.update', $record['id']),
            ['status' => !$record['status']],
            'patch'
        );

        return $actions;
    }

    /**
     * @return array
     */
    public function getFilters()
    {
        $defaultFilters = GridDataHelper::getDefaultFilters();
        unset($defaultFilters['visible']);

        $addFilters = [];
        return array_merge($defaultFilters, $addFilters);
    }
}
