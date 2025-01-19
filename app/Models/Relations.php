<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Relations
 *
 * @property int $id
 * @property int $product_id
 * @property int $instance_id
 * @property int $type
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class Relations extends Model
{
    const TYPE_CATEGORIES = 0;
    const TYPE_INSTRUCTORS = 1;

    /**
     * @var string $table
     */
    protected $table = 'relations';

    protected $fillable = [
        'product_id',
        'instance_id',
        'type'
    ];
}
