<?php

use Hobosoft\MegaLoader\MegaLoader;

define('ROOTPATH', dirname(__DIR__, 2));
require_once __DIR__ . '/../MegaLoader.php';

$replaceStrings['replaceStrings'] =[
    'Symfony\\Bundle\\' => 'bundles/framework-bundle/',
    'Application' => 'app',
    'Plugins' => 'plugins',
    'Library' => 'lib',
    'Source' => 'src',
];

//include("../../Bootloader/TinyLogger.php");

$ldr = new MegaLoader(null, $replaceStrings);
//$ldr->setLogger(new \Hobosoft\Megaloader\TinyLogger());

//$ldr = new \Hobosoft\Megaloader\PsrLoader(new \Psr\Log\NullLogger());

$reg = new \Library\Logger\Registry\Registry();
