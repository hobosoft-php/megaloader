<?php

use Library\Classloader\Classloader;

define('ROOTPATH', dirname(__DIR__, 2));
require_once __DIR__ . '/../Classloader.php';

$replaceStrings['replaceStrings'] =[
    'Symfony\\Bundle\\' => 'bundles/framework-bundle/',
    'Application' => 'app',
    'Plugins' => 'plugins',
    'Library' => 'lib',
    'Source' => 'src',
];

//include("../../Bootloader/TinyLogger.php");

$ldr = new Classloader(null, $replaceStrings);
//$ldr->setLogger(new \Library\Classloader\TinyLogger());

//$ldr = new \Library\Classloader\PsrLoader(new \Psr\Log\NullLogger());

$reg = new \Library\Logger\Registry\Registry();
