<?php

namespace Modules\Notifications\Entities;

use Illuminate\Database\Eloquent\Model;

/**
 * Class NotificationTemplate
 *
 * @package Modules\Notifications\Entities
 *
 * @property string $body
 * @property string $key
 * @property string $subject
 * @property string send_copy_to
 * @property bool $status
 */
class NotificationTemplate extends Model
{
    protected $guarded = ['id'];
    protected $casts = ['status' => 'boolean'];

}
