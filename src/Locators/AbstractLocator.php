<?php

namespace Hobosoft\MegaLoader\Locators;

use Hobosoft\Config\Contracts\ConfigInterface;
use Hobosoft\MegaLoader\MegaLoader;
use Hobosoft\MegaLoader\Contracts\LocatorInterface;
use Psr\Log\LoggerInterface as PsrLoggerInterface;

abstract class AbstractLocator implements LocatorInterface
{
    public function __construct(
        protected MegaLoader $loader,
    )
    {
    }

    abstract public function locate(string $name): string|bool;
}