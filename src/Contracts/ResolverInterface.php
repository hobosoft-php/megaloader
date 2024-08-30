<?php

namespace Hobosoft\MegaLoader\Contracts;

interface ResolverInterface
{
    public function resolve(string $name, mixed $type = null): mixed;
}
