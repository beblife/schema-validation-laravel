<?php

namespace Beblife\SchemaValidation\Tests;

use Beblife\SchemaValidation\SchemaValidationServiceProvider;
use Illuminate\Validation\ValidationException;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    /**
     * @param \Illuminate\Foundation\Application $app
     */
    protected function getPackageProviders($app): array
    {
        return [
            SchemaValidationServiceProvider::class,
        ];
    }

    /**
     * @param \Illuminate\Foundation\Application $app
     */
    protected function getEnvironmentSetUp($app): void
    {
        $app['config']->set('schema-validation.spec_path', $this->specFixture('packages.v1.json'));
    }

    protected function specFixture(string $name): string
    {
        return __DIR__ . '/__fixtures__/specs/' . $name;
    }

    protected function schemaFixture(string $name): string
    {
        return __DIR__ . '/__fixtures__/schemas/' . $name;
    }

    protected function assertFormattedValidationException(ValidationException $exception, string $param, string $message): void
    {
        $this->assertEquals(400, $exception->status);

        $this->assertEquals([
            $param => [
                $message,
            ],
        ], $exception->errors());
    }
}
