<?php

namespace App\Helpers;

use Illuminate\Support\Arr;

class GridDataHelper
{
    const ACTION_TYPE_VUE_ROUTE = 0;
    const ACTION_TYPE_VUE_METHOD = 1;

    /**
     * @param string $title
     * @param int $type // 0 - vueroute , 1 - vuemethod
     * @param array $params
     * @return array
     */
    public static function generateAction($title, $type, $params)
    {
        return [
            'title' => $title,
            'type' => $type,
            'params' => $params
        ];
    }

    /**
     * @param string $route
     * @param array $params
     * @return array
     */
    public static function routeParams($route, $params = [])
    {
        return [
            'route' => $route,
            'params' => $params
        ];
    }

    /**
     * @param string $methodName
     * @param array $params
     * @return array
     */
    public static function vueMethodParams($methodName, $params = [])
    {
        return [
            'methodName' => $methodName,
            'params' => $params
        ];
    }

    /**
     * @param string $actionName
     * @param string $route
     * @param array $routeParams
     * @return array
     */
    public static function generateActionApi($title, $route, $routeParams = [], $method = 'post')
    {
        $params = self::vueMethodParams('actionApi', [
            'route' => $route,
            'method' => $method,
            'routeParams' => $routeParams
        ]);

        return self::generateAction($title, self::ACTION_TYPE_VUE_METHOD, $params);
    }

    /**
     * @param string $actionName
     * @param string $route
     * @param array $routeParams
     * @return array
     */
    public static function generateActionLink($title, $route, $routeParams = [])
    {
        $params = self::routeParams($route, $routeParams);
        return self::generateAction($title, self::ACTION_TYPE_VUE_ROUTE, $params);
    }

    /**
     * @param string $methodName
     * @param string $title
     * @param string $params
     * @return array
     */
    public static function generateActionMethod($methodName, $title,  $params = [])
    {
        $params = self::vueMethodParams($methodName, $params);
        return self::generateAction($title, self::ACTION_TYPE_VUE_METHOD, $params);
    }

    /**
     * @param string $url
     * @param int $id
     * @param string $title
     * @return array
     */
    public static function generateDeleteButton($url, $title)
    {
        // 'icon' - 'trash',
        $params = self::vueMethodParams('deleteEntry', ['url' => $url]);
        return self::generateAction($title, self::ACTION_TYPE_VUE_METHOD, $params);
    }

    /**
     * @param string $routeName
     * @param int $id
     * @param string $title
     * @return array
     */
    public static function generateEditButton($routeName, $id, $title)
    {
        //icon - pencil-square
        $params = self::routeParams($routeName, ['id' => $id]);
        return self::generateAction($title, self::ACTION_TYPE_VUE_ROUTE, $params);
    }

    /**
     * @param string $routeName
     * @param int $id
     * @param string $title
     * @return array
     */
    public static function generateShowButton($routeName, $id, $title)
    {
        //icon - eye
        $params = self::routeParams($routeName, ['id' => $id]);
        return self::generateAction($title, self::ACTION_TYPE_VUE_ROUTE, $params);
    }

    /**
     * @return array
     */
    public static function getDefaultFilters()
    {
        $visibleOptions = Arr::prepend(
            \App\Models\Products::$statusOptions,
            __('system.show_all'),
            ''
        );

        return [
            'search' => self::generateFilter('text', [
                'placeholder' => __('system.search'),
                'className' => 'w-30',
                'value' => ''
            ]),
            'visible' => self::generateFilter('select', [
                'className' => '',
                'placeholder' => __('system.status'),
                'options' => $visibleOptions,
                'value' => ''
            ])
        ];
    }

    /**
     * @param $type
     * @return array
     */
    public static function generateFilter($type, $options = [])
    {
        return [
            'type' => $type,
            'options' => $options
        ];
    }
}
