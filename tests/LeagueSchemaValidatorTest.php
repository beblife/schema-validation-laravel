<?php

namespace Beblife\SchemaValidation\Tests;

use Beblife\SchemaValidation\Exceptions\InvalidSchema;
use Beblife\SchemaValidation\Exceptions\UnableToValidateSchema;
use Beblife\SchemaValidation\Facades\Schema;
use Beblife\SchemaValidation\SchemaValidator;
use Beblife\SchemaValidation\Validators\LeagueSchemaValidator;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Request as SymfonyRequest;
use TypeError;

class LeagueSchemaValidatorTest extends TestCase
{
    public function test_it_can_construct_from_a_valid_json_specification_file(): void
    {
        $validator = new LeagueSchemaValidator($this->specFixture('packages.v1.json'));

        $this->assertInstanceOf(SchemaValidator::class, $validator);
    }

    public function test_it_can_construct_from_a_valid_yaml_specification_file(): void
    {
        $validator = new LeagueSchemaValidator($this->specFixture('packages.v1.yaml'));

        $this->assertInstanceOf(SchemaValidator::class, $validator);
    }

    public function test_it_throws_an_exception_when_constructing_with_unsupported_files(): void
    {
        $thrown = false;

        try {
            new LeagueSchemaValidator($this->specFixture('packages.v1.txt'));
        } catch(TypeError $exception) {
            $thrown = true;
        }

        $this->assertTrue($thrown, 'Failed asserting an exception was thrown.');
    }

    public function test_it_cannot_validate_unknown_request_paths_when_using_schemas_from_file(): void
    {
        $validator = new LeagueSchemaValidator($this->specFixture('validation.v30.json'));
        $request = $this->createRequest('GET', '/unknown');

        try {
            $validator->validate($request);
        } catch(UnableToValidateSchema $exception) {
            $this->assertEquals('Could not find schema for request [/unknown] to validate.', $exception->getMessage());

            return;
        }

        $this->fail('A validation exception was not thrown when validating unknown request');
    }

    /**
     * @dataProvider invalidData
     *
     * @param mixed $value
     */
    public function test_it_can_validate_query_parameters_on_a_request(string $param, $value, string $message): void
    {
        $key = explode('.', $param)[0];

        $validator = new LeagueSchemaValidator($this->specFixture('validation.v30.json'));
        $request = $this->createRequest('GET', '/query-parameters', array_merge([
            'required_field' => true,
        ], [
            $key => $value,
        ]));

        try {
            $validator->validate($request);
        } catch(InvalidSchema $exception) {
           $this->assertFormattedValidationException($exception, $param, $message);

            return;
        }

        $this->fail('A validation exception was not thrown for parameter: '. $param);
    }

    /**
     * @dataProvider invalidData
     *
     * @param mixed $value
     */
    public function test_it_can_validate_a_request_against_a_specified_schema(string $param, $value, string $message): void
    {
        $key = explode('.', $param)[0];

        $schema = Schema::fromFile($this->specFixture('schemas/all-validation.json'));
        $validator = new LeagueSchemaValidator($this->specFixture('packages.v1.json'));
        $request = $this->createRequest('GET', '/', array_merge([
            'required_field' => true,
        ], [
            $key => $value,
        ]));

        try {
            $validator->validate($request, $schema);
        } catch(InvalidSchema $exception) {
            $this->assertFormattedValidationException($exception, $param, $message);

            return;
        }

        $this->fail('A validation exception was not thrown for parameter: required');
    }

    /**
     * @dataProvider invalidData
     *
     * @param mixed $value
     */
    public function test_it_can_validate_the_body_on_a_request(string $param, $value, string $message): void
    {
        $key = explode('.', $param)[0];

        $validator = new LeagueSchemaValidator($this->specFixture('validation.v30.json'));
        $request = $this->createRequest('POST', '/body-parameters', array_merge([
            'required_field' => true,
        ], [
            $key => $value,
        ]));

        try {
            $validator->validate($request);
        } catch(InvalidSchema $exception) {
           $this->assertFormattedValidationException($exception, $param, $message);

            return;
        }

        $this->fail('A validation exception was not thrown for parameter: '. $param);
    }

    public function invalidData(): array
    {
        return [
            'Required value' => [
                'required_field',
                null,
                'Value cannot be null.',
            ],
            'Minimum value' => [
                'strict_limit',
                0,
                'Value must be greater than 1.',
            ],
            'Maximum value' => [
                'strict_limit',
                50,
                'Value must be less than 25.',
            ],
            'Exclusive limit minimum value' => [
                'limit',
                0,
                'Value must be greater or equal to 1.',
            ],
            'Exclusive limit maximum value' => [
                'limit',
                50,
                'Value must be less or equal to 25.',
            ],
            'Min length' => [
                'length',
                '1234',
                'Length must be longer or equal to 5.',
            ],
            'Max length' => [
                'length',
                '123456789',
                'Length must be shorter or equal to 8.',
            ],
            'Wrong type' => [
                'limit',
                'not a number',
                "Value expected to be 'number', 'string' given.",
            ],
            'Mixed type' => [
                'mixed',
                false,
                'Data must match exactly one schema, but matched none.',
            ],
            'Wrong format' => [
                'email',
                'jane.doe',
                'Value does not match format email of type string.',
            ],
            'Not in enum' => [
                'enum',
                'not-in-enum',
                'Value must be present in the enum.',
            ],
            'Invalid array value' => [
                'number_array.1',
                [
                    1,
                    'not a number',
                ],
                "Value expected to be 'integer', 'string' given.",
            ],
            'Invalid array object key' => [
                'object_array.0.field',
                [
                    ['field' => 'not a number'],
                ],
                "Value expected to be 'integer', 'string' given.",
            ],
            'Min items' => [
                'object_array',
                [],
                'Size must be greater or equal to 1.',
            ],
            'Max items' => [
                'object_array',
                [
                    ['field' => 1],
                    ['field' => 2],
                    ['field' => 3],
                ],
                'Size must be less or equal to 2.',
            ],
            'Unique items' => [
                'object_array',
                [
                    ['field' => 1],
                    ['field' => 1],
                ],
                'All items must be unique.',
            ],
            'Required properties' => [
                'object.required_field',
                [
                    'required_field' => null,
                ],
                'Value cannot be null.',
            ],
            'Required nested properties' => [
                'object.nested.required_field',
                [
                    'required_field' => true,
                    'nested' => [
                        'required_field' => null,
                    ],
                ],
                'Value cannot be null.',
            ],
            'Min object properties' => [
                'object_properties',
                [],
                'Object properties must be greater or equal to 1.',
            ],
            'Max object properties' => [
                'object_properties',
                [
                    'one' => 'one',
                    'two' => 'two',
                    'three' => 'three',
                ],
                'Object properties must be less or equal to 2.',
            ],
            'Invalid byte file' => [
                'file_byte',
                'not a byte',
                'Value does not match format byte of type string.',
            ],
            'Invalid pattern' => [
                'pattern',
                'invalid-pattern',
                "Data does not match pattern '#^(\([0-9]{3}\))?[0-9]{3}-[0-9]{4}$#'.",
            ],
            'Invalid multiple of' => [
                'multiple_of',
                5,
                'Division by 2 did not resulted in integer.',
            ],
        ];
    }

    protected function createRequest($method, $uri, $data = [], $headers = []): Request
    {
        $files = $this->extractFilesFromDataArray($data);
        $content = null;
        $parameters = [];

        if($method === 'GET') {
            $parameters = $data;
        }

        if($method === 'POST') {
            $content = json_encode($data);
        }

        $headers = array_merge([
            'CONTENT_LENGTH' => mb_strlen($content, '8bit'),
            'CONTENT_TYPE' => 'application/json',
            'Accept' => 'application/json',
        ], $headers);

        $baseRequest = SymfonyRequest::create(
            $this->prepareUrlForRequest($uri),
            $method,
            $parameters,
            $this->prepareCookiesForJsonRequest(),
            $files,
            array_replace($this->serverVariables, $this->transformHeadersToServerVars($headers)),
            $content
        );

        return Request::createFromBase($baseRequest);

    }
}
