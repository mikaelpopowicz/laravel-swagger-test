<?php

namespace Mp\LaravelSwaggerTest\Parsers;

use Mp\LaravelSwaggerTest\Contracts\SwaggerParser;
use OpenApi\Annotations\MediaType;
use OpenApi\Annotations\OpenApi;
use OpenApi\Annotations\Operation;
use OpenApi\Annotations\PathItem;
use OpenApi\Annotations\Property;
use OpenApi\Annotations\Response;
use OpenApi\Annotations\Schema;
use const OpenApi\UNDEFINED;

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
     * @param string $mediaType
     * @return array
     */
    public function getResponseStructure(string $path, string $method, int $code, string $mediaType = 'application/json'): array
    {
        $media = $this->getResponseMediaType($path, $method, $code, $mediaType);

        return $this->getSchema($media->schema);
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

    public function getResponseMediaType(string $path, string $method, int $code, string $mediaType = 'application/json'): MediaType
    {
        $response = $this->getResponse($path, $method, $code);
        $content = data_get($response, "content.{$mediaType}");

        if (empty($content)) {
            throw new \RuntimeException("Unable to get {$path} [{$method}] : {$code} content of media type {$mediaType}");
        }

        return $content;
    }

    public function getSchema(Schema $schema): array
    {
        $data = [];

        switch ($schema->type) {
            case 'array':
                $items = $schema->items;
                if ($items->ref !== UNDEFINED && starts_with($items->ref, '#/')) {
                    $data[] = $this->getSchema($this->api->ref($items->ref));
                } else if ($items->schema !== UNDEFINED) {
                    dd('schema');
                    $data[] = $this->getSchema($schema);
                }
                break;
            case 'object':
                foreach ($schema->properties as $property) {
                    if ($property->ref && starts_with($property->ref, '#/')) {
                        $data[$property->property] = $this->getSchema($this->api->ref($property->ref));
                    } else if ($property->type === 'array') {
                        //dump($property);
                        $sub = $this->getSchema($property);
                        if (empty($sub)) {
                            $data[] = $property->property;
                        } else {
                            $data[$property->property] = $sub;
                        }
                    } else {
                        $data[] = $property->property;
                    }
                }
                break;
        }

        return $data;
    }
}
