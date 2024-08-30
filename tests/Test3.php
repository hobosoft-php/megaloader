<?php

use Hobosoft\Config\Config;
use Hobosoft\Finders\FileFinder;
use Hobosoft\Logger\Logger;
use Hobosoft\MegaLoader\MegaLoader;
use Hobosoft\MegaLoader\MiniLoader;

define('ROOTPATH', dirname(__DIR__));

require_once __DIR__.'/../vendor/autoload.php';
require_once __DIR__.'/../src/bootstrap.php';

$loaderConfig = [
    'cache' => [
        'enabled' => false,
    ],
    'classmap' => [
        'src/'
    ],
    'psr-4' => [
        'Hobosoft\\MegaLoader\\Tests\\' => __DIR__.'/tests/',
        'Hobosoft\\MegaLoader\\' => __DIR__.'/src/',
    ],
];

$loader = new MiniLoader($loaderConfig['psr-4']);
$config = new Config(null, null, [ 'megaloader' => $loaderConfig ]);
$logger = new Logger($config);

$loader = $loader->createMegaLoader($logger, $config);

$finder = new FileFinder();

//$logger = new TinyLogger();
//$config = new Config($logger, null, []);
//$config[MegaLoader::CONFIG_SECTION] = $loaderConfig;



//$loader = (new MegaLoader($logger, $config))->setMiniLoader($loader);
//$c = new \Hobosoft\MegaLoader\Tests\Classes\NullDatabase();
//$c->method('test');