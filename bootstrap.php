<?php

use Hobosoft\Config\Env;
use Hobosoft\MegaLoader\MegaLoader;
use Hobosoft\MegaLoader\MiniLoader;

if(!defined('ROOTPATH')) {
    throw new RuntimeException('ROOTPATH is not defined');
}

require_once __DIR__ . '/src/MiniLoader.php';

$loader = MiniLoader::create();
$loader = MegaLoader::create($loader);

new Env();

return $loader;
