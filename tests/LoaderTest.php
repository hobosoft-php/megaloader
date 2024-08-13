<?php

namespace Hobosoft\MegaLoader\Tests;

use Hobosoft\Boot\Tiny\TinyLogger;
use Hobosoft\MegaLoader\Contracts\LocatorInterface;
use Hobosoft\MegaLoader\MegaLoader;
use Hobosoft\Config\Config;
use Hobosoft\MegaLoader\Tests\Classes\NullDatabase;
use Hobosoft\MegaLoader\Tests\Plugins\TestPlugin\src\TestPlugin;
use Psr\Log\NullLogger;

define('ROOTPATH', dirname(__DIR__));
require __DIR__.'/../vendor/autoload.php';

function cfg()
{
    $config = new Config(new TinyLogger(), null, []);
    $config[MegaLoader::CONFIG_SECTION] = [
        'cache' => [
            'enabled' => false,
        ],
        'classMap' => [
            'src'
        ],
        'psr-4' => [
            'Hobosoft\\MegaLoader\\' => 'src/',
            'Hobosoft\\MegaLoader\\Tests\\' => 'tests/',
        ],
        'plugins' => [
            'Hobosoft\\Plugins\\' => 'tests/Plugins/',
            'Hobosoft\\Plugin\\' => 'tests/Plugins/',
        ],
        'modules' => [
            'Hobosoft\\Modules\\' => 'tests/Modules/',
            'Hobosoft\\Module\\' => 'tests/Modules/',
        ]
    ];
    return $config;
}
it('class load', function () {
    $loader = new MegaLoader(new TinyLogger(), cfg());
    $n = new NullDatabase();
    expect($n)->toBeInstanceOf(NullDatabase::class);
});
it('plugin load', function () {
    $loader = new MegaLoader(new TinyLogger(), cfg());
    $loader->load('Hobosoft\\Plugin\\TestPlugin', 'plugin');
    $n = new TestPlugin();
    expect($n)->toBeInstanceOf(TestPlugin::class);
});
