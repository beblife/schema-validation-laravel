<?php

namespace Beblife\SchemaValidation\Exceptions;

use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;

class InvalidSchema extends ValidationException
{
    public $status = Response::HTTP_BAD_REQUEST;

    public static function becauseInvalidKeyword(string $key, string $message): self
    {
        return self::withMessages([
            $key => [
                $message,
            ],
        ]);
    }
}
