<?php

namespace Beblife\SchemaValidation\Tests;

use Beblife\SchemaValidation\Facades\Schema;
use Beblife\SchemaValidation\Facades\SchemaValidation;
use Illuminate\Http\Request;

class RequestTest extends TestCase
{
    public function test_it_can_call_to_validate_the_request_schema(): void
    {
        $validator = SchemaValidation::spy();

        $request = Request::createFromGlobals()->validateSchema();

        $validator->shouldHaveReceived('validate', function ($receivedRequest) use ($request) {
            $this->assertEquals($request, $receivedRequest);

            return true;
        });
    }

    public function test_it_can_call_to_validate_the_request_for_a_specified_schema(): void
    {
        $validator = SchemaValidation::spy();
        $schema = Schema::fromArray([
            'type' => 'object',
            'properties' => [],
        ]);

        $request = Request::createFromGlobals()->validateSchema($schema);

        $validator->shouldHaveReceived('validate', function ($receivedRequest, $receivedSchema) use ($request, $schema) {
            $this->assertEquals($request, $receivedRequest);
            $this->assertEquals($schema, $receivedSchema);

            return true;
        });
    }
}
