<?php

namespace Hobosoft\MegaLoader\Loaders;

use Hobosoft\Boot\Boot;
use Hobosoft\Config\Contracts\ConfigInterface;
use Hobosoft\MegaLoader\Contracts\LoaderInterface;
use Hobosoft\MegaLoader\Utils;
use Psr\Log\LoggerInterface as PsrLoggerInterface;

class ClassLoader extends AbstractLoader
{
    public function __construct(
        PsrLoggerInterface $logger,
        ConfigInterface    $config,
        array              $locators = [],
    ) {
        parent::__construct($logger, $config, $locators);
    }

    public function load(string $name): bool
    {
        Utils::include($name);
        return true;
    }
}
