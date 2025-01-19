<?php

namespace App\Models;

use App\Classes\Enum\Vendors\UserType;
use App\Models\UserData;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\DB;

/**
 * Class VendorUsers
 *
 * @property int $id
 * @property int $user_id
 * @property int $vendor_id
 * @property int $user_type
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class VendorsUsers extends Model
{
    /**
     * @var string $table
     */
    protected $table = 'vendors_users';

    protected $fillable = [
        'user_id',
        'vendor_id',
        'user_type'
    ];

    /**
     * $return array
     */
    public static function getTypes()
    {
        return [
            UserType::USER => __('vendors.user_types.user'),
            UserType::MANAGER => __('vendors.user_types.manager'),
        ];
    }

    /**
     * Get the App\Models\User record associated with the VendorUsers.
     */
    public function getUserType()
    {
        $types = $this->getTypes();
        return array_key_exists($this->user_type, $types) ? $types[$this->user_type] : null;
    }

    /**
     * @param Builder $query
     * @return Builder
     */
    public function scopeSearch($query, $value)
    {
        return $query->whereHas('user', function ($query) use ($value) {
            return $query->whereHas('userData', function ($query) use ($value) {
                $query->where(DB::raw('CONCAT(first_name, " ", last_name)'), 'like', '%' . $value . '%');
            });
        });
    }

    /**
     * Get the App\Models\User record associated with the VendorUsers.
     */
    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    /**
     * Get the App\Models\Vendors record associated with the VendorUsers.
     */
    public function vendor()
    {
        return $this->hasOne(Vendors::class, 'id', 'vendor_id');
    }
}
