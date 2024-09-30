<?php


include __DIR__ . '/../vendor/autoload.php';

$inc = [
    __DIR__ . '/../../packages',
];

$gen = new \Hobosoft\MegaLoader\ClassMapGenerator($inc, []);

$mapFile = __DIR__ . '/var/tmp/map.php';
$rootPath = __DIR__ . '/../../';

$gen->writeMap($mapFile, $rootPath);