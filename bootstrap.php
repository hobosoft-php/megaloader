<?php

include __DIR__ . '/src/MegaLoader.php';

class_alias(\Hobosoft\MegaLoader\Utils::class, 'loaderUtils');

$loader = new \Hobosoft\MegaLoader\MegaLoader(new \Psr\Log\NullLogger(), [], null);

