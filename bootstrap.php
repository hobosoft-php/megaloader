<?php

use Hobosoft\Config\Config;
use Hobosoft\Config\Env;
use Hobosoft\MegaLoader\MegaLoader;
use Hobosoft\MegaLoader\MiniLoader;

if(!defined('ROOTPATH')) {
    throw new RuntimeException('ROOTPATH is not defined');
}

require_once __DIR__ . '/src/MiniLoader.php';

$miniLoader = MiniLoader::create();
$megaLoader = MegaLoader::create($miniLoader);

return $megaLoader;
