<?php

namespace Beblife\SchemaValidation;

use Beblife\SchemaValidation\Facades\SchemaValidation;
use Beblife\SchemaValidation\Schemas\CebeSpecSchema;
use Beblife\SchemaValidation\Validators\LeagueSchemaValidator;
use Illuminate\Http\Request;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class SchemaValidationServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('schema-validation-laravel')
            ->hasConfigFile('schema-validation')
        ;
    }

    public function registeringPackage(): void
    {
        $this->app->bind(SchemaValidator::class, function () {
            return new LeagueSchemaValidator(config()->get('schema-validation.spec_path'));
        });

        $this->app->when(SchemaFactory::class)->needs('$schema')->give(CebeSpecSchema::class);
    }

    public function bootingPackage(): void
    {
        Request::macro('validateSchema', function(?Schema $schema = null): Request {
            /** @var Request */
            $request = $this;

            SchemaValidation::validate($request, $schema);

            return $request;
        });
    }
}
