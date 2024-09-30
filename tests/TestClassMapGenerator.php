<?php


use Hobosoft\MegaLoader\MiniLoader;

define('ROOTPATH', dirname(__DIR__));
chdir(ROOTPATH);

include __DIR__ . '/../vendor/autoload.php';

$inc = [
    __DIR__ . '/../../packages',
];
$ldr = MiniLoader::create(ROOTPATH);

$gen = new \Hobosoft\MegaLoader\ClassMapGenerator($inc, []);

$mapFile = __DIR__ . '/var/tmp/map.php';
$rootPath = __DIR__ . '/../../';

$gen->writeMap($mapFile, $rootPath);