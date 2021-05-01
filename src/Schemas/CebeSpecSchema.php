<?php

namespace Beblife\SchemaValidation\Schemas;

use Beblife\SchemaValidation\Schema;
use cebe\openapi\spec\Schema as SpecSchema;
use TypeError;

class CebeSpecSchema extends SpecSchema implements Schema
{
    public function __construct(array $data = [])
    {
        parent::__construct($data);

        $this->performValidation();

        if(count($this->getErrors())) {
            throw new TypeError($this->getErrors()[0]);
        }
    }

    protected function performValidation()
    {
        $this->requireProperties(['type', 'properties']);

        if($this->type !== 'object') {
            $this->addError("property 'type' must be object, but '{$this->type}' given.");
        }
    }

    public function toArray(): array
    {
        return json_decode(json_encode($this->getSerializableData()), true);
    }
}
