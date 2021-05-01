<?php

namespace Beblife\SchemaValidation\Facades;

use Beblife\SchemaValidation\SchemaValidator;
use Illuminate\Support\Facades\Facade;

/**
 * @method static void validate(\Illuminate\Http\Request $request, ?\Beblife\SchemaValidation\Schema $schema = null)
 *
 * @see \Beblife\SchemaValidation\SchemaValidator
 */
class SchemaValidation extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return SchemaValidator::class;
    }
}
