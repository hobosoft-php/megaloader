<?php

use Hobosoft\MegaLoader\Decorators\CacheLocatorDecorator;
use Hobosoft\MegaLoader\Loaders\ClassLoader;
use Hobosoft\MegaLoader\Loaders\PluginLoader;
use Hobosoft\MegaLoader\Locators\ComposerLocator;
use Hobosoft\MegaLoader\Locators\MapLocator;
use Hobosoft\MegaLoader\Locators\PluginLocator;
use Hobosoft\MegaLoader\Locators\Psr0Locator;
use Hobosoft\MegaLoader\Locators\Psr4Locator;

return [

    'psr-4' => [
        'Hobosoft\\MegaLoader\\Tests\\' => 'tests/',
        'Hobosoft\\MegaLoader\\' => 'src/',
    ],

    'classmap' => [
        'src'
    ],

    'cache' => [
        'enabled' => false,
        'path' => 'var/cache',
    ],

    'prepend' => true,
    'replaceComposer' => true,

    'loaders' => [
        'class' => ClassLoader::class,
        'plugin' => PluginLoader::class,
    ],

    'locators' => [
        'class' => [
            'psr-0' => Psr0Locator::class,
            'psr-4' => Psr4Locator::class,
            'classmap' => MapLocator::class,
            'composer' => ComposerLocator::class,
        ],
        'plugin' => [
            'plugin' => PluginLocator::class,
        ],
    ],

    'decorators' => [
        'class' => [
            'cache' => CacheLocatorDecorator::class,
        ],
    ],

];

