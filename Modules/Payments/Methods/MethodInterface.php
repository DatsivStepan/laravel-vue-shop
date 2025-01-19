<?php

namespace Modules\Payments\Methods;

interface MethodInterface
{
    /**
     * Get Method name
     *
     * @return string
     */
    public function getName();

    /**
     * Get Method key
     *
     * @return string
     */
    public function getKey();

    /**
     * Get Method fields for form
     *
     * @return array
     */
    public function getFields();
}
