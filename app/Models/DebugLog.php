<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class DebugLog
 *
 * @property int $id
 * @property int $type
 * @property string $identifier
 * @property string $log
 *
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class DebugLog extends Model
{
    const TYPE_PAYMENTLOG = 1;
    const TYPE_EXCEPTION = 2;

    /**
     * @var string $table
     */
    protected $table = 'debug_log';

    protected $fillable = [
        'type',
        'identifier',
        'log'
    ];
}
