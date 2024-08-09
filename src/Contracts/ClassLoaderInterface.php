<?php

namespace Library\Classloader\Contracts;

interface ClassLoaderInterface
{
    public function loadClass(string $className): bool;
}
