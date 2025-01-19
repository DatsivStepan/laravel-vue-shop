<?php

namespace App\Repositories\Admin;

use App\Models\User;
use App\Models\VendorsUsers;
use App\Repositories\BaseRepository;
use Exception;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class VendorsUsersRepository extends BaseRepository
{
    /**
     * @return string
     */
    protected function getModelClass(): string
    {
        return VendorsUsers::class;
    }

    /**
     * @param int $vendorId
     * @param int $type
     * @return array
     */
    public static function getAssignUserIds($vendorId, $type)
    {
        return UserLmsData::whereHas('user', function($query) use ($vendorId, $type) {
            return $query->whereHas('userVendors', function($query) use ($vendorId, $type) {
                return $query->where([
                    'vendor_id' => $vendorId,
                    'user_type' => $type
                ]);
            });
        })->pluck('lms_user_id');
    }

    /**
     * @param int $vendorId
     * @param array $userIds
     * @param int $userType
     * @return bool
     */
    public function assign(int $vendorId, array $userIds, int $userType = 0)
    {
        $connectionId = getSetting('connection_id');
        if (!$connection = Connection::where('connection_id', $connectionId)->first()) {
            return [];
        }

        DB::beginTransaction();
        try {

            foreach ($userIds as $id) {
                if (!$lmsUser = User::getLmsUser($id['value'])) {
                    $lmsUser = $connection->createUserByLmsUserId($id['value']);
                }

                if ($lmsUser) {
                    if (!$this->updateOrCreate([
                        'vendor_id' => $vendorId,
                        'user_id' => $lmsUser->id,
                        'user_type' => $userType
                    ])) {
                        DB::rollback();
                        return false;
                    }
                }
            }

            DB::commit();
            return true;
        } catch (Exception $e) {
            DB::rollback();
            throw new Exception($e->getMessage());
        }
    }

    /**
     * @param int $vendorId
     * @param int $perPage
     *
     * @return LengthAwarePaginator
     */
    public function getWithPagination($vendorId, int $perPage)
    {
        return QueryBuilder::for($this->getModelClass())
            ->where('vendor_id', $vendorId)
            ->allowedFilters([
                AllowedFilter::scope('search'),
                'user_type',
                'created_at',
            ])
            ->allowedSorts(['created_at'])
            ->paginate($perPage);
    }

    /**
     * @param array $ids
     * @return bool
     */
    public function deleteAll(array $ids)
    {
        return ($this->getModelClass())::query()
            ->whereIn('id', $ids)->delete();
    }
}
