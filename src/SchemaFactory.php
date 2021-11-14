<?php

namespace Beblife\SchemaValidation;

use Illuminate\Filesystem\Filesystem;
use InvalidArgumentException;
use Symfony\Component\Yaml\Yaml;
use TypeError;

class SchemaFactory
{
    private static string $schema;

    public function __construct(string $schema)
    {
        static::$schema = $schema;
    }

    public function fromArray(array $schema): Schema
    {
        return new static::$schema($schema);
    }

    public function fromFile(string $schemaPath): Schema
    {
        $contents = (new Filesystem())->get($schemaPath);

        try {
            return new static::$schema(json_decode($contents, true) ?? Yaml::parse($contents, Yaml::DUMP_OBJECT));
        } catch(TypeError $e) {
            throw new InvalidArgumentException('Only valid OpenAPI JSON or YAML files are supported.');
        }
    }
}
