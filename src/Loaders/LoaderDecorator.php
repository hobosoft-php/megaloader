<?php

namespace Library\Classloader\Loaders;

use Library\Boot\TinyClassloader;
use Library\Classloader\Contracts\ClassLoaderInterface;
use Library\Config\Definitions\Exceptions\Exception;
use Psr\Log\LoggerInterface as PsrLoggerInterface;

class LoaderDecorator extends AbstractLoader
{
    public function __construct(
        protected mixed       $loader,
        ?PsrLoggerInterface   $logger,
        ?array                $config = null,
    ) {
        parent::__construct($logger, $config, null);
        $this->config = $config ?? self::getDefaultConfig();
    }

    public function getLoader(): ?ClassLoaderInterface
    {
        return $this->loader;
    }

    public function setLoader(mixed $loader): void
    {
        $this->loader = $loader;
    }

    public function lookupClass(string $className): ?string
    {
        return $this->loader->lookupClass($className);
    }
}
