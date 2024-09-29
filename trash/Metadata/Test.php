<?php

use Hobosoft\MegaLoader\Metadata\ClassParser;
use Library\Boot\TinyLogger;

require_once __DIR__ . '/../../../vendor/autoload.php';

require_once __DIR__ . '/../../Boot/TinyLogger.php';
require_once __DIR__ . '/FileCache.php';
require_once __DIR__ . '/ClassParser.php';

$logger = new TinyLogger();
$cache = new FileCache(__DIR__ . '/cache');

$c = new ClassParser($logger, $cache);
$i = $c->getInfo(__DIR__ . '/GenericMetadata.php');

print_r($i);