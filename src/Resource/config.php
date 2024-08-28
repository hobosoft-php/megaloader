<?php

namespace Hobosoft\MegaLoader\Resource;

use Hobosoft\MegaLoader\Loaders\CacheLoader;
use Hobosoft\MegaLoader\Loaders\ClassLoader;
use Hobosoft\MegaLoader\Loaders\PluginLoader;
use Hobosoft\MegaLoader\Locators\CacheLocator;
use Hobosoft\MegaLoader\Locators\FinderLocator;
use Hobosoft\MegaLoader\Locators\MapLocator;
use Hobosoft\MegaLoader\Locators\PluginLocator;
use Hobosoft\MegaLoader\Locators\Psr0Locator;
use Hobosoft\MegaLoader\Locators\Psr4Locator;

return [
    ClassLoader::class => [
        //CacheLocator::class,
        //MapLocator::class,
        Psr4Locator::class,
        Psr0Locator::class,
        //FinderLocator::class,
    ],
    PluginLoader::class => PluginLocator::class,
];