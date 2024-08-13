<?php

namespace Hobosoft\MegaLoader\Contracts;

interface LoaderInterface
{
    public function load(string $name): bool;
}
