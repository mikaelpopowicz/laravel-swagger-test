<?php

namespace Tests;

use L5Swagger\L5SwaggerServiceProvider;
use Orchestra\Testbench\TestCase as OrchestraTestCase;

abstract class TestCase extends OrchestraTestCase
{
    protected function getPackageProviders($app)
    {
        return [
            L5SwaggerServiceProvider::class,
        ];
    }

    protected function isOpenApi()
    {
        return version_compare(config('l5-swagger.swagger_version'), '3.0', '>=');
    }

    protected function setAnnotationsPath(string $path = null)
    {
        $cfg = config('l5-swagger');

        if (! empty($path)) {
            $cfg['paths']['annotations'] = $path;
        } else {
            $cfg['paths']['annotations'] = $this->isOpenApi()
                ? __DIR__.'/storage/annotations/OpenApi'
                : __DIR__.'/storage/annotations/Swagger';
        }

        $cfg['generate_always'] = true;
        $cfg['generate_yaml_copy'] = true;

        $cfg['constants']['L5_SWAGGER_CONST_HOST'] = 'http://my-default-host.com';

        config(['l5-swagger' => $cfg]);
    }
}
