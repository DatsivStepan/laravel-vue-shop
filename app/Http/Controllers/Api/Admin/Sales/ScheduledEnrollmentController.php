<?php

namespace App\Http\Controllers\Api\Admin\Sales;

use App\Http\Controllers\Controller;
use App\Repositories\Admin\ShippingRepository;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use App\Http\Resources\Admin\Sales\ScheduledEnrollmentsCollection;
use App\Repositories\Admin\ScheduledEnrollmentsRepository;

class ScheduledEnrollmentController extends Controller
{
    private $repository;

    /**
     * @param ShippingRepository $repository
     */
    public function __construct(ScheduledEnrollmentsRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index(Request $request)
    {
        return new ScheduledEnrollmentsCollection($this->repository->getWithQuery(getDefaultPaginateCount()));
    }

    /**
     * @param int $id
     * @return Response
     */
    public function enroll(int $id)
    {
        return [
            'status' => $this->repository->enroll($id)
        ];
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return Response
     */
    public function destroy(int $id)
    {
        return $this->repository->deleteEnroll([$id]);
    }
}
