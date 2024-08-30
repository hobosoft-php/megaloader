<?php

namespace Hobosoft\MegaLoader\Contracts;

interface ResolvingLoaderInterface extends ResolverInterface
{
    public function load(string $name, mixed $type = null): bool;
}
