<?php

namespace App\Classes\Enum\Refund;

class ActionAfterRefund
{
    public const KEEP_USER_ENROLLMENT = "keep_user_enrollment";
    public const DISABLE_COURSE_ENROLLMENT = "disable_course_enrollment";
    public const UNENROL_USER_FROM_COURSE = "unenrol_user_from_course";
}
