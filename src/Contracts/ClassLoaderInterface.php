<?php

namespace Hobosoft\MegaLoader\Contracts;

interface ClassLoaderInterface
{
    public function loadClass(string $className): bool;
}
