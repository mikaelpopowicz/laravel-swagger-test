<?php

namespace Mp\LaravelSwaggerTest\Contracts;

use OpenApi\Annotations\Operation;
use OpenApi\Annotations\PathItem;

interface SwaggerParser
{
    /**
     * Get the response structure of the given path, method and http code.
     *
     * @param string $path
     * @param string $method
     * @param int    $code
     * @return array
     */
    public function getResponseStructure(string $path, string $method, int $code): array;

    /**
     * Get a path item.
     *
     * @param string $path
     * @return \OpenApi\Annotations\PathItem
     * @throws \RuntimeException
     */
    public function getPath(string $path): PathItem;

    /**
     * Get an operation of a path item.
     *
     * @param string $path
     * @param string $method
     * @return \OpenApi\Annotations\Operation
     */
    public function getOperation(string $path, string $method): Operation;

    public function getResponse(string $path, string $method, int $code);
}
