<?php

namespace Beblife\SchemaValidation;

interface SchemaFactory
{
    public static function fromArray(array $schema): Schema;

    public static function fromFile(string $schemaPath): Schema;
}
