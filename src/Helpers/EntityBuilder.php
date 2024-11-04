<?php

namespace OkaniYoshiii\Framework\Helpers;

use Exception;
use Nette\PhpGenerator\ClassType;
use Nette\PhpGenerator\Literal;
use Nette\PhpGenerator\PhpFile;
use Nette\PhpGenerator\PhpNamespace;
use OkaniYoshiii\Framework\Contracts\Abstracts\Entity;
use OkaniYoshiii\Framework\Contracts\Attributes\SQLField;
use OkaniYoshiii\Framework\Contracts\Attributes\SQLPrimaryKey;
use OkaniYoshiii\Framework\Contracts\Attributes\SQLTable;
use OkaniYoshiii\Framework\Enums\DataType;
use OkaniYoshiii\Framework\ShellProgram;

class EntityBuilder
{
    private PhpFile $phpFile;
    private PhpNamespace $namespace;
    private ClassType $entity;

    public function __construct(string $entityName)
    {
        $this->phpFile = (new PhpFile)
            ->setStrictTypes(true);
        $this->namespace = $this->phpFile
            ->addNamespace('App\\Entities')
            ->addUse(DataType::class)
            ->addUse(SQLField::class)
            ->addUse(SQLTable::class)
            ->addUse(SQLPrimaryKey::class)
            ->addUse(Entity::class);
        $this->entity = $this->namespace
            ->addClass($entityName)
            ->setExtends(Entity::class);
    }

    public function addProperty(string $name, string $type) : self
    {
        $this->entity
            ->addProperty($name)
            ->setType($type)
            ->setNullable(true);

        return $this;
    }

    public function mapSQLTableToEntity(string $tableName) : self
    {
        $this->entity->addAttribute(SQLTable::class, ['name' => $tableName]);

        return $this;
    }

    public function mapSQLPrimaryKeyToProperty(string $property, string $field) : self
    {
        if(!$this->entity->hasProperty($property)) throw new Exception('Property "' . $property . '" does not exists on Entity');

        $this->entity->getProperty($property)->addAttribute(SQLPrimaryKey::class, ['name' => $field]);

        return $this;
    }

    public function mapSQLFieldToProperty(string $property, string $field, bool $isNullable, DataType $type) : self
    {
        if(!$this->entity->hasProperty($property)) throw new Exception('Property "' . $property . '" does not exists on Entity');

        $this->entity->getProperty($property)->addAttribute(SQLField::class, ['name' => $field, 'isNullable' => $isNullable, 'type' => new Literal('DataType::' . $type->name)]);

        return $this;
    }

    public function addGetterMethod(string $property, string $type) : self
    {
        $this->entity
            ->addMethod('get' . ucfirst($property))
            ->setReturnType($type)
            ->setBody(
                <<<BODY
                    return \$this->{$property};
                BODY
            );

        return $this;
    }

    public function addSetterMethod(string $property, string $type) : self
    {
        $this->entity
            ->addMethod('set' .  ucfirst($property))
            ->setReturnType('self')
            ->setBody(
                <<<BODY
                    \$this->{$property} = \${$property};

                    return \$this;
                BODY
            )
            ->addParameter($property)
            ->setType($type);

        return $this;
    }

    public function createPhpFile() : void
    {
        if(!is_dir(ShellProgram::ENTITIES_DIR)) mkdir(ShellProgram::ENTITIES_DIR);

        $filePath = ShellProgram::ENTITIES_DIR . '/' . $this->entity->getName() . '.php';
        file_put_contents($filePath, $this->phpFile);
    }
}