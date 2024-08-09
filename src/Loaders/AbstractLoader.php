<?php

namespace Library\Classloader\Loaders;

use Library\Classloader\Contracts\ClassLoaderInterface;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface as PsrLoggerInterface;

abstract class AbstractLoader implements ClassLoaderInterface
{
    /**
     * @param PsrLoggerInterface|null $logger
     * @param array|null $config
     */
    public function __construct(
        protected ?PsrLoggerInterface   $logger,
        protected ?array                $config = null,
        protected ?ClassLoaderInterface $fallback = null,
    ) {
        $this->config = $config ?? self::getDefaultConfig();
    }

    public static function getDefaultConfig(): array
    {
        return [
            'cache' => [
                'enabled' => true,
                'backend' => 'fileCache',
                'prefix' => 'loader-',
            ],
            'loaders' => [ Psr4Loader::class, MapLoader::class ],
        ];
    }

    public function getLogger(): ?PsrLoggerInterface
    {
        return $this->logger;
    }

    public function setLogger(PsrLoggerInterface $logger): void
    {
        if($logger instanceof LoggerAwareInterface) {
            $this->logger->setLogger($logger);
        }
        else {
            $this->logger = $logger;
        }
    }

    public function setConfig(?array $config): void
    {
        $this->config = $config;
    }

    public function loadClass(string $className): bool
    {
        if($className === 'Library\\Boot\\Compiler\\Compiler') {
            print('');
        }
        if (($info = $this->lookupClass($className)) === null) {
            $this->logger->info("Lookup failed with loader ".static::class.".");
            return ($this->fallback instanceof ClassLoaderInterface) && $this->fallback->loadClass($className);
        }

        include_once $info;
        return true;
    }
    
    abstract public function lookupClass(string $className): ?string;
}
