<?php

namespace App\Repositories\Admin;

use App\Models\CustomFieldsValues;
use App\Repositories\BaseRepository;

class CustomFieldsValuesRepository extends BaseRepository
{
    /**
     * @return string
     */
    protected function getModelClass(): string
    {
        return CustomFieldsValues::class;
    }

    /**
     * @param int $instanceId
     * @param array $customFilesData
     * @return bool
     */
    public function saveCustomFields(int $instanceId, $customFilesData)
    {
        if ($customFilesData) {
            foreach ($customFilesData as $key => $customData) {
                $customFieldId = str_replace("customField_", "", $customData['name']);
                ($this->getModelClass())::updateOrCreate([
                    'field_id' => $customFieldId,
                    'instance_id' => $instanceId
                ], [
                    'value' => json_encode($customData['value'])
                ]);
            }
            return true;
        }

        return false;
    }
}
