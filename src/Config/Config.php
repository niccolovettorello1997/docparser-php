<?php

declare(strict_types=1);

namespace Niccolo\DocparserPhp\Config;

class Config
{
    public function __construct(
        /** @var array<string, string>*/
        private array $env
    ) {
    }

    /**
     * If called without parameters, will return the whole env.
     * If called with a key, will return the corresponding value, default value if the key is not set.
     *
     * @param string|null $key
     * @param mixed       $default
     *
     * @return mixed
     */
    public function get(
        ?string $key = null,
        mixed $default = null
    ): mixed {
        if (null === $key) {
            return $this->env;
        }

        return $this->env[$key] ?? $default;
    }
}
