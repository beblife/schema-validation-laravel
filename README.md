# Schema Validation Laravel

Validate HTTP-requests using OpenAPI specification files or JSON-schema objects in Laravel.

[![Latest Version on Packagist](https://img.shields.io/packagist/vpre/beblife/schema-validation-laravel.svg?style=flat-square)](https://packagist.org/packages/beblife/schema-validation-laravel)
![PHP from Packagist](https://img.shields.io/packagist/php-v/beblife/schema-validation-laravel?style=flat-square)

## Installation

This package can be installed through Composer:
```
composer require beblife/schema-validation-laravel
```

After that can publish the configuration file with this command:
```
php artisan vendor:publish --provider="Beblife\SchemaValidation\SchemaValidationServiceProvider"
```

Once published you will have a `config/schema-validation.php` file that looks like this:

```php
return [
    'spec_path' => env('SCHEMA_VALIDATION_SPEC_PATH', ''),
];
```

You can define the spec path as a `.env` variable or hardcode the absolute path in the configuration file itself.

## Usage

### Validating Requests
This package provides a macro on the `Illumnite\Http\Request` class that will validate the request.

```php
use Illuminate\Http\Request;

class UserRegistrationRequestHandler extends Controller
{
    public function __invoke(Request $request)
    {
        $request = $request->validateSchema();

        // Process the valid request ...
    }
}
```

When the `validateSchema()` method is called the package will search for a matching path defined in the configured OpenAPI specification file and validate the request against the schema.

There are two possible exceptions that can occur when the validation takes place:

**UnableToValidateSchema**

When a path can't be found in the specification file this exception is thrown.

**InvalidSchema**

When the request does not match the schema defined in the specification file this exception is thrown.
This exception extends the Laravel `ValidationException` which results in a *400 : Bad Request* with the following format:

```json
{
    "message": "The given data is invalid.",
    "errors": {
        "property": [
            "The validation message",
        ]
    }
}
```

The package also provides a trait that can be added on a form request to validate the schema. Behind the scenes this trait hooks into the `prepareForValidation()` method to start validating the request's schema. Afterwards any additional validation defined in the `rules()` method will be handled by the Laravel framework.

```php

use Beblife\SchemaValidation\ValidateSchema;
use Illuminate\Foundation\Http\FormRequest;

class UserRegistrationRequest extends FormRequest
{
    use ValidateSchema;

    public function rules(): array
    {
        return [
            // Your other validation rules ...
        ];
    }
}
```

### Defining Schema's

By default the package will use the schema's defined the configured specification when validating requests.
There is also the option to pass a schema to the `validateSchema()` method using the provided facade:

#### From array
```php
$schema = Beblife\SchemaValidation\Facades\Schema::fromArray([
    'type' => 'object',
    'properties' => [
        'field' => [
            'type' => 'string',
            'enum' => [
                'option 1',
                'option 2',
            ]
        ]
    ]
]);
```
#### From File
```php
// from a JSON-file
$schema = Beblife\SchemaValidation\Facades\Schema::fromFile('/path/to/a/schema/file.json'));
// from a YAML-file
$schema = Beblife\SchemaValidation\Facades\Schema::fromFile('/path/to/a/schema/file.yaml'));
```
#### From Class
```php
use Beblife\SchemaValidation\Schema;

class MyCustomSchema implements Schema
{
    public function toArray(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                // ...
            ]
        ]
    }
}
```

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
