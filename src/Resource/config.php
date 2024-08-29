<?php

namespace Hobosoft\MegaLoader\Resource;

use Hobosoft\MegaLoader\Loaders\CacheLoader;
use Hobosoft\MegaLoader\Loaders\ClassLoader;
use Hobosoft\MegaLoader\Loaders\PluginLoader;
use Hobosoft\MegaLoader\Locators\ComposerLocator;
use Hobosoft\MegaLoader\Locators\FinderLocator;
use Hobosoft\MegaLoader\Locators\MapLocator;
use Hobosoft\MegaLoader\Locators\PluginLocator;
use Hobosoft\MegaLoader\Locators\Psr0Locator;
use Hobosoft\MegaLoader\Locators\Psr4Locator;
use Hobosoft\MegaLoader\MegaLoader;
use Hobosoft\MegaLoader\MegaLoader as ML;

return [
    ClassLoader::class => [
        ComposerLocator::class,
        MapLocator::class,
        Psr4Locator::class,
        Psr0Locator::class,
        FinderLocator::class,
    ],
    PluginLoader::class => PluginLocator::class,
];

/*
$arr = [

    'loaders' => [
        'class' => Def::Loader(ClassLoader::class),
        'plugin' => Def::Loader(PluginLoader::class),
    ],

    'locators' => [
        'psr-0' => Def::Locator(Psr0Locator::class),
        'psr-4' => Def::Locator(Psr4Locator::class),
        'finder' => Def::Locator(FinderLocator::class),
        'plugin' => Def::Locator(PluginLocator::class),
        'classmap' => Def::Locator(MapLocator::class),
        'composer' => Def::Locator(ComposerLocator::class),
    ],

    'decorators' => [
        'cache' => Def::Decorator(CacheLoader::class),
    ],

    'hierarchy' => [

        'class' => [

            'class' => [
                'composer',
                'classmap',
                'psr-4',
                'psr-0',
                'finder',
            ],
        ],

        'plugin' => [
            'plugin',
            'psr-4',
            'psr-0',
            'finder',
        ]
    ],

    MegaLoader::class => [
        ClassLoader::class => [
            ComposerLocator::class,
            MapLocator::class,
            Psr4Locator::class,
            Psr0Locator::class,
            FinderLocator::class,
        ],
        CacheLoader::class => [
            ComposerLocator::class,
            MapLocator::class,
            Psr4Locator::class,
            Psr0Locator::class,
            FinderLocator::class,
        ],
        PluginLoader::class => PluginLocator::class,
    ],
];
*/
