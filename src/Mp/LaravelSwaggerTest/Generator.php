<?php

namespace Mp\LaravelSwaggerTest;

use BadMethodCallException;
use L5Swagger\Generator as BaseGenerator;
use Mp\LaravelSwaggerTest\Parsers\OpenApiParser;
use ReflectionException;
use ReflectionMethod;

/**
 * Class Generator
 *
 * @package Mp\LaravelSwaggerTest
 * @method array getResponseStructure(string $path, string $method, int $code, string $mediaType = 'application/json')
 */
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

    public function __call($method, $arguments)
    {
        if (!method_exists($this->parser, $method)) {
            throw new BadMethodCallException("{$method} is undefined");
        }

        try {
            $ref = new ReflectionMethod($this->parser, $method);
            if (!$ref->isPublic()) {
                $visibility = $ref->isPrivate() ? 'private' : 'protected';
                throw new BadMethodCallException("Fatal error : call to {$visibility} {$method}");
            }

            return $this->parser->{$method}(...$arguments);
        } catch (ReflectionException $reflectionException) {
            throw new BadMethodCallException("{$method} is undefined", $reflectionException->getCode(), $reflectionException);
        }
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
}
