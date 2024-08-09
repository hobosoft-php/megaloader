<?php

namespace Hobosoft\MegaLoader\Loaders;

use Hobosoft\MegaLoader\Contracts\ClassLoaderInterface;
use Psr\Log\LoggerInterface as PsrLoggerInterface;

class LoaderDecorator extends AbstractLoader
{
    public function __construct(
        ?PsrLoggerInterface   $logger,
        ?ClassLoaderInterface $loader,
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
