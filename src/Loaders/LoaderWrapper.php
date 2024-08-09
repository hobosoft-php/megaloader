<?php

namespace Library\Classloader\Loaders;

use Library\Classloader\Contracts\ClassLoaderInterface;
use Psr\Log\LoggerInterface as PsrLoggerInterface;

class LoaderWrapper extends AbstractLoader
{
    public function __construct(
        ?ClassLoaderInterface $loader,
        ?PsrLoggerInterface   $logger,
        ?array                $config = null,
    ) {
        parent::__construct($logger, $config, null);
        $this->fallback = $loader;
        $this->config = $config ?? self::getDefaultConfig();
    }

    public function getLoader(): ?ClassLoaderInterface
    {
        return $this->fallback;
    }

    public function setLoader(?ClassLoaderInterface $loader): void
    {
        $this->fallback = $loader;
    }

    public function lookupClass(string $className): ?string
    {
        return (is_null($this->fallback) === true) ? null : $this->fallback->lookupClass($className);
    }
}
