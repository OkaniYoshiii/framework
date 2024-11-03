<?php

namespace OkaniYoshiii\Framework\Helpers;

use Exception;
use Nette\PhpGenerator\ClassType;
use Nette\PhpGenerator\PhpFile;
use Nette\PhpGenerator\Property;
use OkaniYoshiii\Framework\Contracts\Attributes\SQLField;
use OkaniYoshiii\Framework\ShellProgram;

class EntityBuilder
{
    private PhpFile $phpFile;
    private ClassType $entity;

    public function __construct(string $entityName)
    {
        $this->phpFile = (new PhpFile)
            ->setStrictTypes(true);
        $namespace = $this->phpFile
            ->addNamespace('App\\Entities')
            ->addUse(SQLField::class);
        $this->entity = $namespace
            ->addClass($entityName);
    }

    public function addProperty(string $name, string $type) : self
    {
        $property = $this->entity
            ->addProperty($name)
            ->setType($type)
            ->setNullable(true);

        return $this;
    }

    public function mapSQLFieldToProperty(string $property, string $field, bool $isNullable) : self
    {
        if(!$this->entity->hasProperty($property)) throw new Exception('Property "' . $property . '" does not exists on Entity');

        $this->entity->getProperty($property)->addAttribute(SQLField::class, ['name' => $field, 'isNullable' => $isNullable]);

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