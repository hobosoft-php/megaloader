<?php

use Hobosoft\Boot\Tiny\TinyLogger;
use Hobosoft\Config\Config;
use Hobosoft\MegaLoader\MegaLoader;
use Psr\Log\NullLogger;

define('ROOTPATH', dirname(__DIR__));
require_once __DIR__.'/../vendor/autoload.php';

//$loader = new \Hobosoft\Boot\Tiny\TinyClassloader(new NullLogger(), []);
$logger = new TinyLogger();
$config = new Config($logger, null, []);

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
        'Hobosoft\\Plugins\\' => 'plugins/',
    ],
    'modules' => [
        'Hobosoft\\Modules\\' => 'modules/',
    ]
];

$loader = new MegaLoader($logger, $config);

$c = new \Hobosoft\MegaLoader\Tests\Classes\NullDatabase();

$c->method('test');