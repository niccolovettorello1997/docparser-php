<?php

declare(strict_types=1);

namespace DocparserPhp\Core;

use ReflectionClass;
use ReflectionException;
use ReflectionNamedType;

class Container
{
    /** @var array<class-string, (callable(Container): object)|object> $instances */
    private array $instances = [];

    /**
     * Set a custom factory for a class.
     *
     * @param class-string $id
     * @param callable     $factory
     *
     * @return void
     */
    public function set(string $id, callable $factory): void
    {
        $this->instances[$id] = $factory;
    }

    /**
     * Get an instance for a class.
     *
     * @param class-string $id
     *
     * @throws \Exception
     * @throws \RuntimeException
     *
     * @return object
     */
    public function get(string $id): object
    {
        if (isset($this->instances[$id]) && is_callable($this->instances[$id])) {
            /** @var object $factoryApplied */
            $factoryApplied = ($this->instances[$id])($this);

            $this->instances[$id] = $factoryApplied;
        }

        if (!isset($this->instances[$id])) {
            // Try to auto-resolve using reflection
            $this->instances[$id] = $this->autoResolve($id);
        }

        /** @var object $resultingInstance */
        $resultingInstance = $this->instances[$id];

        return $resultingInstance;
    }

    /**
     * Try to auto resolve a class if possible.
     *
     * @param class-string $class
     *
     * @throws \Exception
     * @throws \RuntimeException
     *
     * @return object
     */
    private function autoResolve(string $class): object
    {
        try {
            $reflector = new ReflectionClass($class);

            if (!$reflector->isInstantiable()) {
                throw new \Exception(sprintf('Class %s is not instantiable', $class));
            }

            $constructor = $reflector->getConstructor();

            if (!$constructor) {
                return new $class();
            }

            $params = [];
            foreach ($constructor->getParameters() as $param) {
                /** @var null|ReflectionNamedType $paramType */
                $paramType = $param->getType();

                $paramClass = $paramType ? $paramType->getName() : null;

                if ($paramClass && class_exists($paramClass)) {
                    $params[] = $this->get($paramClass);
                } else {
                    $params[] = null;
                }
            }

            return $reflector->newInstanceArgs($params);
        } catch (ReflectionException $e) {
            throw new \RuntimeException(sprintf('Cannot resolve %s: %s', $class, $e->getMessage()));
        }
    }
}
