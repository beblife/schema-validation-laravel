<?php

namespace Beblife\SchemaValidation\Tests\Contracts;

use InvalidArgumentException;

trait SchemaFactoryTests
{
    abstract public function getSchemaFactoryClass(): string;

    public function test_it_can_construct_from_a_json_file(): void
    {
        $schema = $this->getSchemaFactoryClass()::fromFile($this->schemaFixture('example.json'));

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
        $schema = $this->getSchemaFactoryClass()::fromFile($this->schemaFixture('example.yaml'));

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
            $this->getSchemaFactoryClass()::fromFile($this->schemaFixture('example.txt'));
        } catch(InvalidArgumentException $exception) {
            $thrown = true;
        }

        $this->assertTrue($thrown, 'Failed asserting an exception was thrown.');
    }

    public function test_can_be_constructed_from_array(): void
    {
        $data = [
            'type' => 'object',
            'properties' => [
                'example' => [
                    'type' => 'string',
                ],
            ],
        ];

        $schema = $this->getSchemaFactoryClass()::fromArray($data);

        $this->assertEquals($data, $schema->toArray());
    }
}
