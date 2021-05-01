<?php

namespace Beblife\SchemaValidation\Tests;

use Beblife\SchemaValidation\Facades\Schema;
use Beblife\SchemaValidation\Facades\SchemaValidation;
use Beblife\SchemaValidation\Schema as SchemaContract;
use Beblife\SchemaValidation\ValidateSchema;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

class ValidateSchemaTest extends TestCase
{
    public function test_it_can_validate_the_schema_for_a_form_request(): void
    {
        $validator = SchemaValidation::spy();
        Route::get('/form-request', function (SchemaFormRequestStub $request) {
            return response($request->all());
        });

        $this->get('/form-request');

        $validator->shouldHaveReceived('validate');
    }

    public function test_it_can_validate_a_specified_schema_for_a_form_request(): void
    {
        $validator = SchemaValidation::spy();
        Route::get('/form-request', function (SpecifiedSchemaFormRequestStub $request) {
            return response($request->all());
        });

        $this->get('/form-request');

        $validator->shouldHaveReceived('validate', function (...$args) {
            $this->assertCount(2, $args);
            $this->assertInstanceOf(Request::class, $args[0]);
            $this->assertInstanceOf(SchemaContract::class, $args[1]);

            return true;
        });
    }
}

/**
 * Form request stubs that will use schema validation
 */

class SchemaFormRequestStub extends FormRequest {

    use ValidateSchema;

    public function rules(): array
    {
        return [];
    }
};

class SpecifiedSchemaFormRequestStub extends FormRequest {

    use ValidateSchema;

    public function schema(): SchemaContract
    {
        return Schema::fromArray([
            'type' => 'object',
            'properties' => [],
        ]);
    }

    public function rules(): array
    {
        return [];
    }
};
