<?php

namespace Mp\LaravelSwaggerTest;

use L5Swagger\Generator as BaseGenerator;
use Mp\LaravelSwaggerTest\Parsers\OpenApiParser;
use OpenApi\Annotations\PathItem;

class Generator extends BaseGenerator
{
    /**
     * @var \Mp\LaravelSwaggerTest\Contracts\SwaggerParser
     */
    protected $parser;

    /**
     * Make a generator with a populated swagger instance.
     *
     * @return self
     */
    public static function make(): self
    {
        return (new static)
            ->defineConstants()
            ->scanFilesForDocumentation()
            ->populateServers()
            ->setParser();
    }

    /**
     * Set inner parser instance.
     *
     * @return self
     */
    protected function setParser(): self
    {
        $this->parser = $this->isOpenApi()
            ? new OpenApiParser($this->swagger)
            : null;

        return $this;
    }

    public function getPath(string $path): PathItem
    {
        return $this->parser->getPath($path);
    }
}
