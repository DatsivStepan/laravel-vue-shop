<?php

namespace Modules\Payments\Helpers;

use Symfony\Component\Finder\Finder;

class FunctionHelper
{
    /**
     * @return array
     */
    public static function getMethods()
    {
        $path = str_replace('\\', '/', config('payments.namespace')) . '/Methods';

        $finder = new Finder();
        $finder->files()->name('*Method.php')->in(base_path() . $path);
        $methods = [];

        foreach ($finder as $file) {
            $methods[] = str_replace('/', '\\', $path . '/' . $file->getBasename('.php'));
        }

        return $methods;
    }

    /**
     * @return array
     */
    public static function getMethodPath($methodName)
    {
        $path = str_replace('\\', '/', config('payments.namespace')) . '/Methods';

        return str_replace('/', '\\', $path . '/' . $methodName . 'Method');
    }

    /**
     * @param string $type
     * @param string $name
     * @param string $label
     * @param array $options
     * @return array
     */
    public static function prepareMethodField(string $type, string $name, string $label, $options = [])
    {
        return [
            'name' => $name,
            'label' => $label,
            'type' => $type,
            'values' => $options
        ];
    }
}
