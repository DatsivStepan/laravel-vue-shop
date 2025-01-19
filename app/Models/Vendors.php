<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * Class Vendors
 *
 * @property int $id
 * @property string $name
 * @property string $idnumber
 * @property string $type
 * @property string $email
 * @property string $company
 * @property string $url
 * @property string $address
 * @property int $status
 *
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class Vendors extends Model
{
    const STATUS_INACTIVE = 0;
    const STATUS_ACTIVE = 1;

    public static $statusOptions = [
        self::STATUS_INACTIVE => 'Inactive',
        self::STATUS_ACTIVE => 'Active'
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

    public static $typeOptions = [
        0 => 'Corp',
        1 => 'K12',
        2 => 'Highered'
    ];

    /**
     * @var string $table
     */
    protected $table = 'vendors';

    protected $fillable = [
        'name',
        'idnumber',
        'type',
        'email',
        'company',
        'url',
        'address',
        'status'
    ];

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
     * @return null|string
     */
    public function getImageUrl()
    {
        return $this->files ? $this->files->getFileSrc() : null;
    }

    /**
     * Get the App\Models\VendorsAssings record associated with the Vendors.
     */
    public function assigns()
    {
        return $this->hasMany(VendorsAssings::class, 'vendor_id', 'id');
    }

    /**
     * @return BelongsToMany
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, VendorsUsers::class, 'vendor_id', 'user_id')
            ->withPivot('id');
    }

    /**
     * Get the App\Models\Files record associated with the Vendors.
     */
    public function files()
    {
        return $this->hasOne(Files::class, 'instance_id', 'id')
            ->where([
                'instance_key' => 'image',
                'instance_type' => Files::INSTANCE_TYPE_VENDORS
            ]);
    }
}
