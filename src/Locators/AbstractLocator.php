<?php

namespace Hobosoft\MegaLoader\Locators;

use Hobosoft\Config\Contracts\ConfigInterface;
use Hobosoft\MegaLoader\Contracts\LocatorInterface;
use Psr\Log\LoggerInterface as PsrLoggerInterface;

abstract class AbstractLocator implements LocatorInterface
{
    public function __construct(
        protected PsrLoggerInterface $logger,
        protected ConfigInterface    $config,
    ) {
    }

    //abstract public function locate(string $className): string|bool;
}