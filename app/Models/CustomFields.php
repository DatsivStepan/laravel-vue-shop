<?php

namespace App\Models;

use App\Classes\Enum\CustomFieldType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * Class CustomFields
 *
 * @property int $id
 * @property string $title
 * @property int $required
 * @property int $visible
 * @property int $field_type
 * @property string $options
 * @property string $instance_type
 * @property int $sortorder
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class CustomFields extends Model
{
    public static $typesWithOptions = [
        CustomFieldType::FIELD_TYPE_SELECT,
        CustomFieldType::FIELD_TYPE_MULTISELECT
    ];

    const STATUS_INACTIVE = 0;
    const STATUS_ACTIVE = 1;

    public static $statusOptions = [
        self::STATUS_INACTIVE => 'Inactive',
        self::STATUS_ACTIVE => 'Active'
    ];

    const STATUS_NOT_REQUIRED = 0;
    const STATUS_REQUIRED = 1;

    public static $requiredOptions = [
        self::STATUS_NOT_REQUIRED => 'No',
        self::STATUS_REQUIRED => 'Yes'
    ];

    /**
     * @var string $table
     */
    protected $table = 'custom_fields';

    protected $fillable = [
        'title',
        'required',
        'visible',
        'field_type',
        'options',
        'instance_type',
        'sortorder'
    ];

    /**
     * @return array
     */
    public static function getSearchFields()
    {
        return [
            'title'
        ];
    }

    /**
     * @return array
     */
    public static function getDefaultSort()
    {
        return [
            'column' => 'sortorder',
            'direction' => 'desc'
        ];
    }

    /**
     * @param Builder $query
     * @return Builder
     */
    public function scopeSearch($query, $value)
    {
        return $query->where('title', 'like', '%' . $value . '%');
    }

    /**
     * @param int $instance_id
     * @return string
     */
    public function getValue($instance_id)
    {
        $fValue = $this->customFieldValue()->where('instance_id', $instance_id)->first();

        if (!$fValue) {
            return '';
        }

        $options = $this->getOptions();
        $resValue = json_decode($fValue->value);
        $ressValue = json_decode($fValue->value);

        switch ($this->field_type) {
            case CustomFieldType::FIELD_TYPE_SELECT:
            case CustomFieldType::FIELD_TYPE_CHECKBOX:
                $resValue = $ressValue ? __('system.yes') : __('system.no');
                break;
            case CustomFieldType::FIELD_TYPE_MULTISELECT:
                if (is_array($resValue)) {
                    $values = $resValue;
                    $resValue = implode(", ", array_filter($options, function($k) use ($values) {
                        return array_key_exists($k, $values);
                    }, ARRAY_FILTER_USE_KEY));
                }
                break;
        }

        return $resValue;
    }

    /**
     * @return array
     */
    public function getOptions()
    {
        return $this->options ? preg_split('/[\n\r]+/', $this->options) : [];
    }

    /**
     * @return string
     */
    public function getFieldType()
    {
        $fieldTypes = self::getFieldTypes();
        return array_key_exists($this->field_type, $fieldTypes) ?
            $fieldTypes[$this->field_type] : '';
    }

    /**
     * @return array
     */
    public static function getFieldTypes()
    {
        return [
            CustomFieldType::FIELD_TYPE_TEXT => __('custom_fields.types.text'),
            CustomFieldType::FIELD_TYPE_SELECT => __('custom_fields.types.select'),
            CustomFieldType::FIELD_TYPE_MULTISELECT => __('custom_fields.types.multi_select'),
            CustomFieldType::FIELD_TYPE_CHECKBOX => __('custom_fields.types.checkbox'),
            CustomFieldType::FIELD_TYPE_TEXTAREA => __('custom_fields.types.textarea'),
            CustomFieldType::FIELD_TYPE_DATE => __('custom_fields.types.date'),
            CustomFieldType::FIELD_TYPE_DATETIME => __('custom_fields.types.datetime')
        ];
    }

    /**
     * Get the App\Models\CustomFieldsValues record associated with the CustomFields.
     */
    public function customFieldValue()
    {
        return $this->hasOne(CustomFieldsValues::class, 'field_id', 'id');
    }
}
