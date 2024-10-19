<?php

namespace OkaniYoshiii\Framework\Types;

use Exception;
use OkaniYoshiii\Framework\Enums\DataType;
use OkaniYoshiii\Framework\Types\Primitive\CamelCaseWord;
use OkaniYoshiii\Framework\Types\Primitive\FQCN;

class PhpClassFile
{
    private readonly string $namespace;
    private readonly CamelCaseWord $name;
    private readonly bool $isStrictTyped;
    private array $properties;
    private array $methods;

    public function __construct(string $namespace, CamelCaseWord $name)
    {
        $this->namespace = $namespace;
        $this->name = $name;
    }

    public function setIsStrictTyped(bool $isStrictTyped = true) : self
    {
        $this->isStrictTyped = $isStrictTyped;

        return $this;
    }

    public function addProperty(CamelCaseWord $name, DataType|FQCN|null $type = null, string|int|float|array|bool|null $defaultValue = 'void') : self
    {
        $this->properties[] = [
            'name' => $name,
            'type' => $type,
            'default' => $defaultValue
        ];
        
        return $this;
    }

    public function addMethod(CamelCaseWord $name, DataType|FQCN|null $returnType = null, string $content) : self
    {
        $this->methods[] = [
            'name' => $name,
            'returnType' => $returnType,
            'content' => $content
        ];

        return $this;
    }

    public function buildFile(string $path) : void
    {
        if(!is_dir($path)) throw new Exception($path . ' is not a valid directory : directory does not exists');


    }
}