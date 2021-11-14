<?php

namespace Beblife\SchemaValidation\Tests;

use Beblife\SchemaValidation\Factories\CebeSpecSchemaFactory;
use Beblife\SchemaValidation\Tests\Contracts\SchemaFactoryTests;

final class CebeSpecSchemaFactoryTest extends TestCase
{
    use SchemaFactoryTests;

    public function getSchemaFactoryClass(): string
    {
        return CebeSpecSchemaFactory::class;
    }

}
