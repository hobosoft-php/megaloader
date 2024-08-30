<?php

use Hobosoft\Boot\PathEnum;
use Hobosoft\Boot\Paths;
use Hobosoft\Boot\Tiny\TinyLogger;
use Hobosoft\Config\Config;
use Hobosoft\MegaLoader\Utils;

define('ROOTPATH', dirname(__DIR__));

require_once __DIR__.'/../vendor/autoload.php';

$whoops = new \Whoops\Run;
$whoops->pushHandler(new \Whoops\Handler\PlainTextHandler);
$whoops->register();

//\Tracy\Debugger::enable(\Tracy\Debugger::Development);

$logger = new TinyLogger();
$config = new Config($logger, null, []);
$config[\Hobosoft\MegaLoader\MegaLoader::CONFIG_SECTION] = [
    'cache' => [
        'enabled' => false,
        'path' => Paths::get(PathEnum::CACHE),//ROOTPATH . '/var/cache',
    ],
    'classmap' => [
        'src'
    ],
    'psr-4' => [
        'Hobosoft\\MegaLoader\\' => 'src/',
        'Hobosoft\\MegaLoader\\Tests\\' => 'tests/',
    ],
];

register_shutdown_function(function () {
    $p = Utils::joinPaths(ROOTPATH, PathEnum::VAR, 'debug/included_files-all.txt');
    file_put_contents($p, print_r(get_included_files(), true));
});

$ml = new \Hobosoft\MegaLoader\MegaLoader($logger, $config);
$i = new Hobosoft\MegaLoader\Metadata\ClassInfo(__CLASS__, __FILE__);

print('end'.PHP_EOL);
print('end'.PHP_EOL);
print('end'.PHP_EOL);
print('end'.PHP_EOL);
print('end'.PHP_EOL);
print('end'.PHP_EOL);
print('end'.PHP_EOL);
print('end'.PHP_EOL);
print('end'.PHP_EOL);
print('end'.PHP_EOL);
print('end'.PHP_EOL);
print('end'.PHP_EOL);
print('end'.PHP_EOL);
print('end'.PHP_EOL);
print('end'.PHP_EOL);
print('end'.PHP_EOL);
/*
$c = new \Hobosoft\MegaLoader\Composer\Composer(
    new \Psr\Log\NullLogger(),
    dirname(__FILE__, 2),
    10,
    [],
    [],
);

$a = $c->get('autoload');
print_r($a);
*/
