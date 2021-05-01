<?php

namespace Beblife\SchemaValidation\Tests;

use Beblife\SchemaValidation\SchemaFactory;
use InvalidArgumentException;

final class SchemaFactoryTest extends TestCase
{
    public function test_it_can_construct_from_a_json_file(): void
    {
        $schema = SchemaFactory::fromFile($this->schemaFixture('example.json'));

        $this->assertEquals([
            'type' => 'object',
            'properties' => [
                'example' => [
                    'type' => 'string',
                ],
            ],
        ], $schema->toArray());
    }

    public function test_it_can_construct_from_a_yaml_file(): void
    {
        $schema = SchemaFactory::fromFile($this->schemaFixture('example.yaml'));

        $this->assertEquals([
            'type' => 'object',
            'properties' => [
                'example' => [
                    'type' => 'string',
                ],
            ],
        ], $schema->toArray());
    }

    public function test_it_throws_an_exception_when_constructing_with_unsupported_files(): void
    {
        $thrown = false;

        try {
            SchemaFactory::fromFile($this->schemaFixture('example.txt'));
        } catch(InvalidArgumentException $exception) {
            $thrown = true;
        }

        $this->assertTrue($thrown, 'Failed asserting an exception was thrown.');
    }
}
