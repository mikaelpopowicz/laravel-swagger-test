<?php

namespace Mp\LaravelSwaggerTest\Parsers;

use Mp\LaravelSwaggerTest\Contracts\SwaggerParser;
use OpenApi\Annotations\OpenApi;
use OpenApi\Annotations\Operation;
use OpenApi\Annotations\PathItem;
use OpenApi\Annotations\Response;

class OpenApiParser implements SwaggerParser
{
    /**
     * @var \OpenApi\Annotations\OpenApi
     */
    private $api;

    public function __construct(OpenApi $openApi)
    {
        $this->api = $openApi;
    }

    /**
     * Get the response structure of the given path, method and http code.
     *
     * @param string $path
     * @param string $method
     * @param int    $code
     * @return array
     */
    public function getResponseStructure(string $path, string $method, int $code): array
    {
        // TODO: Implement getResponseStructure() method.
    }

    public function getPath(string $path): PathItem
    {
        $item = collect($this->api->paths)->firstWhere('path',$path);

        if (empty($item)) {
            throw new \RuntimeException("Unable to get {$path}");
        }

        return $item;
    }

    public function getOperation(string $path, string $method): Operation
    {
        $item = $this->getPath($path);
        $operation = data_get($item, $method);

        if (empty($operation)) {
            throw new \RuntimeException("Unable to get {$path} [{$method}]");
        }

        return $operation;
    }

    public function getResponse(string $path, string $method, int $code): Response
    {
        $operation = $this->getOperation($path, $method);
        $response = collect($operation->responses)->firstWhere('response', $code);

        if (empty($response)) {
            throw new \RuntimeException("Unable to get {$path} [{$method}] : {$code}");
        }

        return $response;
    }
}
