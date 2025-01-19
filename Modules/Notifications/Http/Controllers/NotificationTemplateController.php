<?php

namespace Modules\Notifications\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Notifications\Entities\NotificationTemplate;
use Modules\Notifications\Http\Resources\NotificationTemplateCollection;
use Modules\Notifications\Repositories\NotificationTemplateRepository;
use Modules\Notifications\Http\Resources\NotificationTemplateResource;
use Illuminate\Http\JsonResponse;

class NotificationTemplateController extends Controller
{
    /**
     * @var NotificationTemplateRepository
     */
    private $repository;

    /**
     * NotificationTemplateController constructor.
     *
     * @param NotificationTemplateRepository $repository
     */
    public function __construct(NotificationTemplateRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function index()
    {
        $perPage = request('per_page', 10);

        $notifications = $this->repository->paginate($perPage);

        return (new NotificationTemplateCollection($notifications))->response();
    }

    /**
     * Display the specified resource.
     *
     * @param NotificationTemplate $notificationTemplate
     *
     * @return JsonResponse
     */
    public function show(NotificationTemplate $notificationTemplate)
    {
        return (new NotificationTemplateResource($notificationTemplate))->response();
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param NotificationTemplate $notificationTemplate
     *
     * @return JsonResponse
     */
    public function update(Request $request, NotificationTemplate $notificationTemplate)
    {
        $payload = $request->validate([
            'body' => 'string|min:2',
            'status' => 'boolean',
            'subject' => 'nullable|string',
            'send_copy_to' => 'nullable|email',
        ]);

        $notificationTemplate = $this->repository->update($notificationTemplate, $payload);

        return (new NotificationTemplateResource($notificationTemplate))->response();
    }
}
