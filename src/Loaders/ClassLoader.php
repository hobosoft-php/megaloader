<?php

namespace Hobosoft\MegaLoader\Loaders;

use Hobosoft\Config\Contracts\ConfigInterface;
use Hobosoft\MegaLoader\Contracts\LocatorInterface;
use Hobosoft\MegaLoader\MegaLoader;
use Hobosoft\MegaLoader\Utils;
use Psr\Log\LoggerInterface as PsrLoggerInterface;

class ClassLoader extends AbstractLoader
{
    const string TYPE = 'class';

    public function load(string $name): bool
    {
        return ($located = $this->locate($name)) && Utils::include($located);
    }
}
