<?php

namespace App\Helpers;

use stdClass;

class FunctionHelper
{
    /**
     * @param array $array
     * @param bool $multiselect
     * @return array
     */
    public static function arrayToVueOptions($array, $multiselect = false)
    {
        $res = [];
        if ($array) {
            foreach ($array as $key => $value) {
                $model = new stdClass();
                if ($multiselect) {
                    $model->name = $value;
                } else {
                    $model->text = $value;
                }
                $model->value = $key;
                $res[] = $model;
            }
        }

        return $res;
    }

    /**
     * @param Illuminate\Database\Eloquent\Collection $object
     * @param string $fieldValue
     * @param string $fieldText
     * @param bool $multiselect
     * @return array
     */
    public static function modelsToVueOptions($object, $fieldValue, $fieldText, $multiselect = false)
    {
        $res = [];
        if ($object) {
            foreach ($object as $model) {
                $nModel = new stdClass();
                if ($multiselect) {
                    $nModel->name = $model->{$fieldText};
                } else {
                    $nModel->text = $model->{$fieldText};
                }
                $nModel->value = $model->{$fieldValue};
                $res[] = $nModel;
            }
        }

        return $res;
    }
}
