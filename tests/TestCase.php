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

    protected function setAnnotationsPath()
    {
        $cfg = config('l5-swagger');
        $cfg['paths']['annotations'] = __DIR__.'/storage/annotations/Swagger';

        if ($this->isOpenApi()) {
            $cfg['paths']['annotations'] = __DIR__.'/storage/annotations/OpenApi';
        }

        $cfg['generate_always'] = true;
        $cfg['generate_yaml_copy'] = true;

        //Adding constants which will be replaced in generated json file
        $cfg['constants']['L5_SWAGGER_CONST_HOST'] = 'http://my-default-host.com';

        config(['l5-swagger' => $cfg]);
    }
}
