<?php

namespace Hobosoft\MegaLoader\Locators;

use Hobosoft\MegaLoader\Contracts\LocatorInterface;

class PluginLocator implements LocatorInterface
{
    public function locate(string $name): string|bool
    {
        return false;
    }
}