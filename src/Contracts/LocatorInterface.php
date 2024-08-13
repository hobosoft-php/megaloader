<?php

namespace Hobosoft\MegaLoader\Contracts;

interface LocatorInterface
{
    public function locate(string $className): string|bool;
}
