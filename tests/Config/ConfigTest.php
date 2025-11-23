<?php

declare(strict_types=1);

namespace DocparserPhp\Tests\Config;

use DocparserPhp\Config\Config;
use PHPUnit\Framework\TestCase;

class ConfigTest extends TestCase
{
    public function test_constructor_sets_env(): void
    {
        $env = ['foo' => 'bar', 'baz' => 'gag'];
        $config = new Config(env: $env);

        // Get method called without key parameter will return the whole env
        $this->assertEquals($env, $config->get());
    }

    public function test_get_correct_key(): void
    {
        $env = ['foo' => 'bar', 'baz' => 'gag'];
        $config = new Config(env: $env);

        $this->assertEquals($env['foo'], $config->get(key: 'foo'));
        $this->assertEquals($env['baz'], $config->get(key: 'baz'));
    }

    public function test_get_incorrect_key_defaults(): void
    {
        $defaultValue = 'default';
        $env = ['foo' => 'bar', 'baz' => 'gag'];
        $config = new Config(env: $env);

        $this->assertEquals($defaultValue, $config->get(key: 'poo', default: $defaultValue));
    }
}
