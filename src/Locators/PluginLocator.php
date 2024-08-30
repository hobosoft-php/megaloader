<?php

namespace Hobosoft\MegaLoader\Locators;

use Hobosoft\MegaLoader\Contracts\LocatorInterface;

class PluginLocator implements LocatorInterface
{
    public function locate(string $pluginName): string|bool
    {
        return false;
    }
}