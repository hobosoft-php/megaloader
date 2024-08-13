<?php

namespace Hobosoft\MegaLoader\Locators;

class PluginLocator extends AbstractLocator
{
    public function locate(string $pluginName): string|bool
    {
        return false;
    }
}