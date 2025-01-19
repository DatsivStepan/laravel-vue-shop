<?php

namespace App\Services;

use App\Classes\Enum\Product\EnrollEndType;
use App\Classes\Enum\Product\EnrollStartType;
use App\Models\Connection;
use App\Models\Orders;
use Carbon\Carbon;
use Exception;

class EnrollService
{
    /**
     * @param Orders $order
     * @param null|int $onlyProductId
     *
     * @return Orders
     * @throws Exception
     *
     */
    public function enrollForOrder(Orders $order, $onlyProductId = null)
    {
        if (!$role = $order->user->getLmsRole()) {
            return false;
        }

        $connectionId = getSetting('connection_id');
        if (!$connection = Connection::where('connection_id', $connectionId)->first()) {
            return false;
        }

        $products = $order->products();

        if ($onlyProductId) {
            $products->where('product_id', $onlyProductId);
        }

        $enrolments = [];
        foreach ($products->get() as $product) {
            $courses = $product->courses;
            $now = Carbon::now()->timestamp;

            if ($product->courses()->exists()) {

                if (!$onlyProductId && ($product->enrol_start == EnrollStartType::SPECIFIC_DATE)
                && $product->enrol_start_date && ($product->enrol_start_date > $now)) {
                    continue;
                }

                foreach ($courses as $course) {
                    if ($course->isOver()) {
                        continue;
                    }
                    $courseId = $course['course_id'];
                    $timeStart = time();
                    switch ($product->enrol_start) {
                        case EnrollStartType::COURSE_START:
                            if ($course->start_date) {
                                $timeStart = Carbon::parse($course->start_date)->timestamp;
                            }
                        break;
                        case EnrollStartType::SPECIFIC_DATE:
                            if ($product->enrol_start_date) {
                                $timeStart = Carbon::parse($product->enrol_start_date)->timestamp;
                            }
                        break;
                    }
                    $timeEnd = $timeStart;
                    switch ($product->enrol_end) {
                        case EnrollEndType::BASED_OF_DURATION:
                            $timeEnd += $product->enrol_period;
                        break;
                        case EnrollEndType::COURSE_END:
                            $timeEnd = Carbon::parse($course->end_date)->timestamp;
                        break;
                    }

                    if ($onlyProductId && ($timeStart > $now)) {
                        $timeStart = $now;
                    }

                    $enrolments[] = [
                        'userid' => $lmsUserId,
                        'courseid' => $courseId,
                        'role' => $role,
                        'timestart' => $timeStart,
                        'timeend' => $timeEnd,
                        'suspend' => 0
                    ];
                }
            }
        }

        if ($enrolments) {
            return $connection->enrollUsersApi($enrolments);
        }

        return [];
    }
}
