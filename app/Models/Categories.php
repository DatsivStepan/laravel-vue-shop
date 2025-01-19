<?php

namespace App\Models;

use App\Helpers\FunctionHelper;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Categories
 *
 * @property int $id
 * @property string $name
 * @property string $description
 * @property int $parent_id
 * @property int $visible
 * @property string $id_number
 * @property int $sortorder
 * @property int $dept
 * @property string $path
 * @property int $max_product_per_user
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class Categories extends Model
{
    const STATUS_INACTIVE = 0;
    const STATUS_ACTIVE = 1;

    public static $statusOptions = [
        self::STATUS_INACTIVE => 'Inactive',
        self::STATUS_ACTIVE => 'Active'
    ];

    protected $fillable = [
        'name',
        'description',
        'parent_id',
        'visible',
        'id_number',
        'sortorder',
        'dept',
        'path',
        'max_product_per_user'
    ];

    /**
     * @return array
     */
    public static function getSearchFields()
    {
        return [
            'name'
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
     * @return null|string
     */
    public function getImageUrl()
    {
        return $this->files ? $this->files->getFileSrc() : null;
    }

    /**
     * @param null|File $image
     */
    public static function getListInArray()
    {
        $models = self::select('id', 'name')->get();

        return FunctionHelper::modelsToVueOptions($models, 'id', 'name', true);
    }

    /**
     * @param Builder $query
     * @param mixed $value
     * @return Builder
     */
    public function scopeSearch($query, $value)
    {
        return $query->where('name', 'like', '%' . $value . '%');
    }

    /**
     * @param Builder $query
     * @param mixed $value
     * @return Builder
     */
    public function scopeParentId($query, $value)
    {
        return $query->where('parent_id', $value);
    }

    /**
     * @return int
     */
    public function getProductsCount()
    {
        return count($this->productsCategories);
    }

    /**
     * Get the App\Models\Categories record associated with the Categories.
     */
    public function parentCategory()
    {
        return $this->hasOne(self::class, 'id', 'parent_id');
    }

    /**
     * Get the App\Models\ProductCategories record associated with the Categories.
     */
    public function productsCategories()
    {
        return $this->hasMany(ProductCategories::class, 'category_id', 'id');
    }

    /**
     * Get the App\Models\ProductCategories record associated with the Categories.
     */
    public function childs()
    {
        return $this->hasMany(self::class, 'parent_id', 'id');
    }

    /**
     * Get the App\Models\Files record associated with the Categories.
     */
    public function files()
    {
        return $this->hasOne(Files::class, 'instance_id', 'id')
            ->where([
                'instance_key' => 'image',
                'instance_type' => Files::INSTANCE_TYPE_CATEGORY
            ]);
    }
}
