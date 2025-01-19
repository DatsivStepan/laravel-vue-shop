<?php

namespace App\Models;

use App\Classes\Enum\Order\PaymentStatus;
use App\Classes\Enum\Vendors\UserType;
use App\Models\UserData;
use App\Repositories\Admin\SettingsRepository;
use App\Services\Taxes\Taxation;
use Glorand\Model\Settings\Traits\HasSettingsField;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Spatie\Permission\Traits\HasRoles;
use Throwable;
use Tymon\JWTAuth\Contracts\JWTSubject;

/**
 * Class User
 *
 * @property int $id
 * @property string $name
 * @property string $email
 * @property string $password
 * @property string $email_verified_at
 * @property string $remember_token
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @method settings
 */
class User extends Authenticatable implements JWTSubject, Taxation
{
    use Notifiable, HasRoles, HasSettingsField;

    protected $guard_name = 'api';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];
    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * @param string $tmpToken
     *
     * @return UserLmsData
     */
    public function getConnectionId()
    {
        $lmsUserData = $this->ltiLmsUserData;

        return $lmsUserData ? $lmsUserData->connection_id : 0;
    }

    /**
     * @param Builder $query
     * @param mixed $value
     * @return Builder
     */
    public function scopeSearch($query, $value)
    {
        return $query->whereHas('userData', function ($query) use ($value) {
            $query->where(DB::raw('CONCAT(first_name, " ", last_name)'), 'like', '%' . $value . '%');
        });
    }


    /**
     * @param Builder $query
     * @param mixed $vendors
     * @return Builder
     */
    public function scopeByVendors($query, ...$vendors)
    {
        return $query->whereHas('userVendors', function ($query) use ($vendors) {
            return $query->whereIn('vendor_id', $vendors);
        });
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return null|string
     */
    public function getRole()
    {
        if (!$roleIds = $this->roles->pluck('id')->toArray()) {
            return null;
        }

        return Roles::whereIn('role_id', $roleIds)
            ->pluck('lms_role_name')
            ->first();
    }

    /**
     * @return string
     */
    public function getFullName()
    {
        $userData = $this->userData;
        return $userData ? $userData->getFullName() : $this->name;
    }

    /**
     * @return string
     */
    public function isAdmin()
    {
        return $this->hasRole(Roles::ROLE_NAME_ADMIN);
    }

    /**
     * @return string
     */
    public function getAvatar()
    {
        $userData = $this->userData;
        return $userData ? $userData->getAvatar() : getDefaultImageSrc();
    }

    /**
     * @return int
     */
    public function getWaitlistCount()
    {
        return $this->productWaitlist()->count();
    }

    /**
     * @return int
     */
    public function getFavoritesCount()
    {
        return $this->ordersFavorites()->count();
    }

    /**
     * @return int
     */
    public function getMyProductCount()
    {
        return $this->orders()->count();
    }

    /**
     * @return int
     */
    public function getMyCoursesCount()
    {
        //TODO: need to be changed, when we add functionality with courses
        return $this->orders()->count();
    }

    /**
     * @return int
     */
    public function getMyOrdersCount()
    {
        return $this->orders()->count();
    }

    /**
     * @return int
     */
    public function countProductInCart()
    {
        $userId = $this->id;
        /** @var Orders $orderInCart */
        $orderInCart = $this->orders()
            ->where('payment_status', PaymentStatus::PAYMENT_STATUS_IN_CART)
            ->where('user_id', $userId)
            ->first();

        return $orderInCart ? $orderInCart->products()->count() : 0;
    }

    /**
     * @return string
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * @param array $roles
     *
     * @return array
     */
    public static function getUsersByRoles($roles)
    {
        return self::select('users.*')->join('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')
            ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
            ->where('model_has_roles.model_type', '=', self::class)
            ->whereIn('roles.guard_name', $roles)
            ->get();
    }

    /**
     * TODO It is system settings not user settings
     *
     * @return array
     */
    public function getUserSettings()
    {
        if (Cache::has('userSetting')) {
            return json_decode(Cache::get('userSetting'));
        }
        $settingRep = new SettingsRepository();
        if ($settings = $settingRep->getSettings(false)) {
            $res = [];
            foreach ($settings as $key => $setting) {
                $res[$setting->key] = $setting->getValue();
            }

            Cache::put('userSetting', json_encode($res), Carbon::now()->addMinutes(60));

            return $res;
        }

        return null;
    }

    /**
     * @return array
     */
    public function getPermissionsInArray()
    {
        return array_map(
            function ($permission) {
                return $permission['name'];
            },
            $this->getAllPermissions()->toArray()
        );
    }

    /**
     * @return array
     */
    public function getRolesInArray()
    {
        return array_map(
            function ($role) {
                return $role['name'];
            },
            $this->roles->toArray()
        );
    }

    /**
     * @return array
     */
    public function getManagerVendorsIds()
    {
        return $this->userVendors()->where(['user_type' => UserType::MANAGER])->pluck('vendor_id')->toArray();
    }

    /**
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [
            'connection_id' => getSetting('connection_id'),
        ];
    }

    /**
     * Get the Role record associated with the User.
     */
    public function modelHasRoles()
    {
        return $this->belongsToMany('Role', 'assigned_roles');
    }

    /**
     * Get the App\Models\ProductInstructors record associated with the User.
     */
    public function productsInstructors()
    {
        return $this->hasMany(ProductInstructors::class, 'instructor_id', 'id');
    }

    /**
     * Get the App\Models\UserData record associated with the Categories.
     */
    public function userData()
    {
        return $this->hasOne(UserData::class, 'user_id', 'id');
    }

    # Model
    public function setPasswordAttribute($value)
    {
        $this->setField('password', $value);
    }

    # Model
    public function setNameAttribute($value)
    {
        $this->setField('name', $value);
    }

    public function getNameAttribute($value)
    {
        return $this->getField('name', $value);
    }

    public function setEmailAttribute($value)
    {
        $this->setField('email', $value);
    }

    public function getEmailAttribute($value)
    {
        return $this->getField('email', $value);
    }

    private function getField($fieldName, $value)
    {
        if ($value != utf8_encode($value)) {
            try {
                $key = getAppKey();
                $formattedValue = DB::connection()->getPdo()->quote($value);
                return DB::select(DB::raw("SELECT AES_DECRYPT($formattedValue, '$key') as $fieldName"))[0]->$fieldName;
            } catch (Throwable $e) {
                Log::info("AES_DECRYPT Fail");
            }
        }
        return $value;
    }

    private function setField($fieldName, $value)
    {
        try {
            $key = getAppKey();
            $this->attributes[$fieldName] = DB::select(DB::raw("SELECT AES_ENCRYPT('$value', '$key') as $fieldName"))[0]->$fieldName;
        } catch (Throwable $e) {
            throw $e;
            $this->attributes[$fieldName] = $value;
        }
    }

    //RELATION

    /**
     * @return HasMany
     */
    public function productWaitlist(): HasMany
    {
        return $this->hasMany(ProductWaitlist::class, 'user_id');
    }

    /**
     * @return HasMany
     */
    public function orders(): HasMany
    {
        return $this->hasMany(Orders::class, 'user_id');
    }

    /**
     * @return HasMany
     */
    public function ordersFavorites(): HasMany
    {
        return $this->hasMany(ProductFavorites::class, 'user_id');
    }

    /**
     * @return HasMany
     */
    public function userVendors(): HasMany
    {
        return $this->hasMany(VendorsUsers::class, 'user_id');
    }

    public function getTaxationClass(): string
    {
        return self::class;
    }

    public function getFields(): array
    {
        return [
            'city',
            'apartment',
            'lms_user_id',
            'email',
        ];
    }
}
