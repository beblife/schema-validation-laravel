<?php

namespace Beblife\SchemaValidation\Validators;

use Beblife\SchemaValidation\Exceptions\InvalidSchema;
use Beblife\SchemaValidation\Exceptions\UnableToValidateSchema;
use Beblife\SchemaValidation\Schema;
use Beblife\SchemaValidation\SchemaValidator;
use cebe\openapi\Reader;
use cebe\openapi\spec\Schema as SpecSchema;
use GuzzleHttp\Psr7\ServerRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use League\OpenAPIValidation\PSR7\Exception\NoPath;
use League\OpenAPIValidation\PSR7\Exception\Validation\InvalidBody;
use League\OpenAPIValidation\PSR7\Exception\Validation\InvalidQueryArgs;
use League\OpenAPIValidation\PSR7\ServerRequestValidator;
use League\OpenAPIValidation\PSR7\ValidatorBuilder;
use League\OpenAPIValidation\Schema\Exception\KeywordMismatch;
use League\OpenAPIValidation\Schema\SchemaValidator as LeagueValidator;
use Psr\Http\Message\ServerRequestInterface;
use TypeError;

class LeagueSchemaValidator implements SchemaValidator
{
    protected ValidatorBuilder $builder;

    protected ServerRequestValidator $validator;

    public function __construct(string $specPath)
    {
        $this->builder = new ValidatorBuilder();

        try {
            $this->validator = $this->builder->fromJsonFile($specPath)->getServerRequestValidator();
        } catch(TypeError $execption) {
            try {
                $this->validator = $this->builder->fromYamlFile($specPath)->getServerRequestValidator();
            } catch(TypeError $execption) {
                throw $execption;
            }
        }
    }

    /**
     * @throws InvalidSchema
     */
    public function validate(Request $request, ?Schema $schema = null): Request
    {
        try {
            if(is_null($schema)) {
                $this->validateFromSpec($request);
            }

            if($schema) {
                $this->validateForSchema($request, $schema);
            }
        } catch(NoPath $exception) {
            throw UnableToValidateSchema::becauseNoSchemaForRequest($request);
        } catch(InvalidQueryArgs $exception) {
            throw $this->validationException($exception->getPrevious()->getPrevious());
        } catch(InvalidBody $exception) {
            throw $this->validationException($exception->getPrevious());
        } catch (KeywordMismatch $keywordMismatch) {
            throw $this->validationException($keywordMismatch);
        }

        return $request;
    }

    protected function validateFromSpec(Request $request): void
    {
        $this->validator->validate($this->toServerRequest($request));
    }

    protected function validateForSchema(Request $request, Schema $schema): void
    {
        $validator = new LeagueValidator(LeagueValidator::VALIDATE_AS_REQUEST);
        $specSchema = Reader::readFromJson(json_encode($schema->toArray()), SpecSchema::class);

        $validator->validate($request->all(), $specSchema);
    }

    protected function toServerRequest(Request $request): ServerRequestInterface
    {
        $serverRequest = new ServerRequest(
            $request->method(),
            $request->getUri(),
            $request->header(),
            $request->getContent(),
            $request->getProtocolVersion(),
            $request->server()
        );

        $serverRequest = $serverRequest->withQueryParams($request->query());

        return $serverRequest;
    }

    protected function validationException(KeywordMismatch $keywordMismatch): InvalidSchema
    {
        $key = implode('.', $keywordMismatch->dataBreadCrumb()->buildChain());

        return InvalidSchema::becauseInvalidKeyword($key, $this->messageForInvalidKeyword($keywordMismatch));
    }

    protected function messageForInvalidKeyword(KeywordMismatch $keywordMismatch): string
    {
        $data = $keywordMismatch->data();

        if(is_array($data)) {
            $data = json_encode($data);
        }

        if(is_null($data)) {
            $data = ' ';
        }

        if(! is_string($data)) {
            $data = (string) $data;
        }

        $message = $keywordMismatch->getMessage();

        foreach([
            'Keyword validation failed: ' => '',
            'Value ' . $data => 'Value',
            "Value '{$data}'" => 'Value',
            "Length of '{$data}'" => 'Length',
            'Size of an array' => 'Size',
            'All array' => 'All',
            "The number of object's" => 'Object',
         ] as $search => $replace) {
            $message = str_replace($search, $replace, $message);
         }

        if(Str::endsWith($message, '.')) {
            return $message;
        }

        return $message . '.';
    }
}
