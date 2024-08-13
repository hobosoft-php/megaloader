<?php

namespace Hobosoft\MegaLoader\Locators;

class Psr0Locator extends AbstractLocator
{
    public function locate(string $className): string|bool
    {
        die("Class ".__CLASS__." is not functional yet.");
    }
}
