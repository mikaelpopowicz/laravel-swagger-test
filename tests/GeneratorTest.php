<?php

namespace Tests;

use Mp\LaravelSwaggerTest\Generator;

class GeneratorTest extends TestCase
{
    /** @test */
    public function can_deduce_pet_structure()
    {
        $structure = [
            [
                'id',
                'category' => [
                    'id',
                    'name',
                ],
                'name',
                'photoUrls',
                'tags' => [
                    [
                        'id',
                        'name',
                    ]
                ]
            ]
        ];

        $this->setAnnotationsPath(__DIR__ . '/../vendor/zircote/swagger-php/Examples/petstore-3.0');
        $generator = Generator::make();

        $responseStructure = $generator->getResponseStructure('/pet/findByTags', 'get', 200);

        $this->assertThat(
            $responseStructure,
            $this->equalTo($structure)
        );
    }
}
