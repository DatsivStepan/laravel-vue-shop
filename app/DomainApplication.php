<?php

namespace App;

use Dotenv\Dotenv;
use Dotenv\Exception\InvalidPathException;
use Illuminate\Foundation\Application;

class DomainApplication extends Application
{
    /**
     * Create a new Illuminate application instance.
     *
     * @param string|null $basePath
     * @return void
     */
    public function __construct($basePath = null)
    {
        if ($basePath) {
            $this->setBasePath($basePath);
        }

        $this->registerBaseBindings();
        $this->registerBaseServiceProviders();
        $this->registerCoreContainerAliases();

        if (isset($_SERVER['HTTP_HOST']) && !empty($_SERVER['HTTP_HOST'])) {
            $domain = $_SERVER['HTTP_HOST'];
        }

        if (isset($domain)) {
            $dotenv = Dotenv::createImmutable(__DIR__ . '/../', '.env.' . $domain);

            try {
                $dotenv->load();
            } catch (InvalidPathException $e) {
                // No custom .env file found for this domain
            }
        }
    }

    /**
     * @return string
     */
    public function langPath()
    {
        return env('APP_LANG_PATH') ??
            $this->resourcePath() . DIRECTORY_SEPARATOR . 'lang';
    }
}
