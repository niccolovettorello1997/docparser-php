<?php

declare(strict_types=1);

namespace DocparserPhp\Tests\Unit\Core;

use DocparserPhp\Config\Config;
use DocparserPhp\Controller\ApiController;
use DocparserPhp\Core\Container;
use DocparserPhp\Model\Utils\Error\AbstractError;
use PHPUnit\Framework\TestCase;

class ContainerTest extends TestCase
{
    public function test_container_set_and_get(): void
    {
        $container = new Container();
        $id = Container::class;
        $lambda = fn (Container $c): object => new \StdClass();

        $container->set(id: $id, factory: $lambda);

        $this->assertInstanceOf(\StdClass::class, $container->get(id: $id));
    }

    public function test_container_get_inexistent_id(): void
    {
        $this->expectException(\RuntimeException::class);

        $container = new Container();

        /** @var class-string<object> $id */
        $id = "DocparserPhp\Core\InexistentClass";

        $container->get(id: $id);
    }

    public function test_container_not_instantiable_class(): void
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage("Class DocparserPhp\\Model\\Utils\\Error\\AbstractError is not instantiable");

        $container = new Container();

        $container->get(AbstractError::class);
    }

    public function test_container_instantiate_class_without_args(): void
    {
        $container = new Container();

        $result = $container->get(Container::class);

        $this->assertInstanceOf(Container::class, $result);
    }

    public function test_container_instantiate_class_with_args(): void
    {
        $container = new Container();

        $container->set(Config::class, fn (): Config => new Config(env: []));

        $result = $container->get(ApiController::class);

        $this->assertInstanceOf(ApiController::class, $result);
    }
}
