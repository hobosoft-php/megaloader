<?php

namespace Hobosoft\MegaLoader\Loaders;

use Hobosoft\Boot\Boot;
use Hobosoft\Config\Contracts\ConfigInterface;
use Hobosoft\MegaLoader\Contracts\LoaderInterface;
use Psr\Log\LoggerInterface as PsrLoggerInterface;

class ClassLoader extends AbstractLoader
{
    public function __construct(
        protected PsrLoggerInterface $logger,
        protected ConfigInterface    $config,
        protected array              $locators = [],
    ) {
        parent::__construct($logger, $config);
    }

    public function load(string $name): bool
    {
        Boot::include($name);
        return true;
    }
}
