<?php

namespace App\Repositories\Admin;

use App\Events\Sessions\Created;
use App\Events\Sessions\Deleted;
use App\Events\Sessions\Updated;
use App\Models\Sessions;
use App\Repositories\BaseRepository;
use Exception;
use Illuminate\Support\Facades\DB;

class SessionsRepository extends BaseRepository
{
    /**
     * @return string
     */
    protected function getModelClass(): string
    {
        return Sessions::class;
    }

    /**
     * @param array $data
     * @return Model
     * @throws Exception
     */
    public function create(array $data)
    {
        DB::beginTransaction();
        try {
            $model = parent::create($this->prepare($data));
            $this->afterSave($model, $data);

            event(new Created($model));

            DB::commit();
            return $model;
        } catch (Exception $e) {
            DB::rollback();
            throw new Exception($e->getMessage());
        }
    }

    /**
     * @param Model $model
     * @param array $data
     * @return Model
     * @throws Exception
     */
    public function update($model, array $data)
    {
        DB::beginTransaction();
        try {
            $model = parent::update($model, $this->prepare($data));
            $this->afterSave($model, $data);

            event(new Updated($model));

            DB::commit();
            return $model;
        } catch (Exception $e) {
            DB::rollback();
            throw new Exception($e->getMessage());
        }
    }

    /**
     * @param Model $model
     * @return Model
     * @throws Exception
     */
    public function delete($model)
    {
        $productId = $model->id;
        $model = parent::delete($model);

        event(new Deleted($productId));

        return $model;
    }

    /**
     * @param Model $model
     * @param array $data
     */
    public function afterSave($model, array $data)
    {
        if (!$model->updated_at && !$model->sequence) {
            $model->sequence = $model->id;
            $model->save();
        }
    }
}
