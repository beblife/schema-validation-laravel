<?php

namespace Beblife\SchemaValidation\Factories;

use Beblife\SchemaValidation\Schema;
use Beblife\SchemaValidation\SchemaFactory;
use Beblife\SchemaValidation\Schemas\CebeSpecSchema;
use Illuminate\Filesystem\Filesystem;
use InvalidArgumentException;
use Symfony\Component\Yaml\Yaml;
use TypeError;

class CebeSpecSchemaFactory implements SchemaFactory
{
    public static function fromArray(array $schema): Schema
    {
        return new CebeSpecSchema($schema);
    }

    public static function fromFile(string $schemaPath): Schema
    {
        $contents = (new Filesystem())->get($schemaPath);

        try {
            return new CebeSpecSchema(json_decode($contents, true) ?? Yaml::parse($contents, Yaml::DUMP_OBJECT));
        } catch(TypeError $e) {
            throw new InvalidArgumentException('Only valid OpenAPI JSON or YAML files are supported.');
        }
    }
}
