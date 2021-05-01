<?php

namespace Beblife\SchemaValidation\Facades;

use Beblife\SchemaValidation\Schema as SchemaContract;
use Beblife\SchemaValidation\SchemaFactory;
use Illuminate\Support\Facades\Facade;

/**
 * @method static SchemaContract fromArray(array $schema)
 * @method static SchemaContract fromFile(string $filePath)
 *
 * @see \Beblife\SchemaValidation\SchemaFactory
 */
class Schema extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return SchemaFactory::class;
    }
}
