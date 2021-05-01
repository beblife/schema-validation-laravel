<?php

namespace Beblife\SchemaValidation\Tests;

use Beblife\SchemaValidation\Schemas\CebeSpecSchema;
use TypeError;

class CebeSpecSchemaTest extends TestCase
{
    public function test_it_throws_an_exception_when_constructing_with_missing_properties_key(): void
    {
        $thrown = false;

        try {
            new CebeSpecSchema([
                'type' => 'string',
            ]);
        } catch(TypeError $exception) {
            $thrown = true;
            $this->assertEquals('CebeSpecSchema is missing required property: properties', $exception->getMessage());
        }

        $this->assertTrue($thrown, 'Failed asserting an exception was thrown.');
    }

    public function test_it_throws_an_exception_when_constructing_with_wrong_type_key(): void
    {
        $thrown = false;

        try {
            new CebeSpecSchema([
                'type' => 'string',
                'properties' => [],
            ]);
        } catch(TypeError $exception) {
            $thrown = true;
            $this->assertEquals("property 'type' must be object, but 'string' given.", $exception->getMessage());
        }

        $this->assertTrue($thrown, 'Failed asserting an exception was thrown.');
    }
}
