<?php

namespace Tests;

use Mp\LaravelSwaggerTest\Generator;

class GeneratorTest extends TestCase
{
    /** @test */
    public function can_get_path()
    {
        $this->setAnnotationsPath();

        $generator = Generator::make();

        $path = $generator->getPath('/projects');
        dd($path);
    }
}