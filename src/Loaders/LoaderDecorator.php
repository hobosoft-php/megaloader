<?php

namespace Hobosoft\MegaLoader\Loaders;

use Hobosoft\MegaLoader\Contracts\LoaderInterface;
use Psr\Log\LoggerInterface as PsrLoggerInterface;

abstract class LoaderDecorator extends AbstractLoader
{
    public function __construct(
        LoaderInterface     $loader,
        array               $config = [],
    ) {
        parent::__construct($loader->getParent(), $config, $config);
        $this->config = $config;
    }

    public function getLoader(): ?LoaderInterface
    {
        return $this->loader;
    }

    public function setLoader(mixed $loader): void
    {
        $this->loader = $loader;
    }

    public function load(string $className): bool
    {
        return $this->loader->loadClass($className);
    }
}
