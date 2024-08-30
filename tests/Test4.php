<?php

use Hobosoft\Logger\Logger;
use Hobosoft\Config\Config;
use Hobosoft\Finders\FileFinder;
use Hobosoft\MegaLoader\MegaLoader;

//require_once __DIR__.'/../vendor/autoload.php';
$loader = require_once __DIR__.'/../bootstrap.php';
$loader = $loader->createMegaLoader();

$config = new Config(null, null, [ 'megaloader' => $loader->getConfig() ]);
$logger = new Logger($config);
$loader->dump();


if(PHP_SAPI === 'cli') {
    require_once __DIR__.'/../vendor/autoload.php';
    \Spatie\Ignition\Ignition::make()
        ->applicationPath(\Hobosoft\MegaLoader\MegaLoader::getRootPath())
        ->register();
}
else {
    $whoops = new \Whoops\Run;
    $whoops->pushHandler(new \Whoops\Handler\PlainTextHandler);
    $whoops->register();
}

trigger_error("asdadasdasdad");
$finder = new FileFinder();

//$logger = new TinyLogger();
//$config = new Config($logger, null, []);
//$config[MegaLoader::CONFIG_SECTION] = $loaderConfig;



//$loader = (new MegaLoader($logger, $config))->setMiniLoader($loader);
//$c = new \Hobosoft\MegaLoader\Tests\Classes\NullDatabase();
//$c->method('test');