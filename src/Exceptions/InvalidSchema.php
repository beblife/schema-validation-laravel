<?php

namespace Beblife\SchemaValidation\Exceptions;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;

class InvalidSchema extends ValidationException
{
    public function __construct(Validator $validator, ?Response $response = null, string $errorBag = 'default')
    {
        parent::__construct($validator, $response, $errorBag);
        $this->status = config('schema-validation.response.status', $this->status);
    }

    public static function becauseMissingRequiredKeyword(string $key, string $message): self
    {
        return self::withMessages([
            $key => [
                $message,
            ],
        ]);
    }

    public static function becauseInvalidKeyword(string $key, string $message): self
    {
        return self::withMessages([
            $key => [
                $message,
            ],
        ]);
    }
}
