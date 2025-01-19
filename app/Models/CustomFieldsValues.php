<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class CustomFieldsValues
 *
 * @property int $id
 * @property int $field_id
 * @property int $instance_id
 * @property string $value
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class CustomFieldsValues extends Model
{
    /**
     * @var string $table
     */
    protected $table = 'custom_fields_values';

    protected $fillable = [
        'field_id',
        'instance_id',
        'value'
    ];

    /**
     * Get the App\Models\CustomFields record associated with the CustomFieldsValues.
     */
    public function customField()
    {
        return $this->hasOne(CustomFields::class, 'id', 'field_id');
    }
}
