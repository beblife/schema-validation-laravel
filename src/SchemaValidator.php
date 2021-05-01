<?php

namespace Beblife\SchemaValidation;

use Beblife\SchemaValidation\Exceptions\InvalidSchema;
use Beblife\SchemaValidation\Exceptions\UnableToValidateSchema;
use Illuminate\Http\Request;

interface SchemaValidator
{
    /**
     * @throws InvalidSchema|UnableToValidateSchema
     */
    public function validate(Request $request, ?Schema $schema = null): Request;
}
