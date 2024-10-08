<?php

namespace Hobosoft\MegaLoader\Contracts;

interface LocatorInterface
{
    public function locate(string $name): array|string|bool;
}
