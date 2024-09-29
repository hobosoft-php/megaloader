<?php

namespace Hobosoft\MegaLoader\Contracts;

interface ResolvingLocatorInterface extends ResolverInterface
{
    public function locate(string $name, mixed $type = null): array|string|bool;
}
