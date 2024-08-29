<?php

namespace Hobosoft\MegaLoader\Contracts;

interface LoaderInterface
{
    public function load(string $name): bool;
    //public function getLocator(): LocatorInterface;
    //public function setLocator(\Closure|LocatorInterface|string|array $loader): void;
}
