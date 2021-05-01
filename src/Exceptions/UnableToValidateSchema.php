<?php

namespace Beblife\SchemaValidation\Exceptions;

use Exception;
use Illuminate\Http\Request;

class UnableToValidateSchema extends Exception
{
    public static function becauseNoSchemaForRequest(Request $request): self
    {
        return new self(sprintf('Could not find schema for request [/%s] to validate.', $request->path()));
    }
}
