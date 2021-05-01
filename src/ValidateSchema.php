<?php

namespace Beblife\SchemaValidation;

use Beblife\SchemaValidation\Facades\SchemaValidation;

trait ValidateSchema
{
    protected function prepareForValidation(): void
    {
        if(method_exists($this, 'schema')) {
            $schema = $this->schema();
        }

        $this->validateSchema($schema ?? null);
    }

    public function validateSchema(?Schema $schema = null): void
    {
        SchemaValidation::validate($this, $schema);
    }
}
