<?php

namespace Hobosoft\MegaLoader\Loaders;

use Hobosoft\MegaLoader\Contracts\ClassLoaderInterface;
use Hobosoft\MegaLoader\Contracts\ManagerInterface;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface as PsrLoggerInterface;

abstract class AbstractLoader implements ClassLoaderInterface
{
    public function __construct(
        protected ManagerInterface    $parent,
        protected ?PsrLoggerInterface $logger,
        protected ?array              $config = null,
    ) {
        $this->config = $config ?? self::getDefaultConfig();
    }

    public static function getDefaultConfig(): array
    {
        return [
            'cache' => [
                'enabled' => true,
                'backend' => 'fileCache',
                'prefix' => 'megaloader-'.PHP_SAPI,
            ],
            'loaders' => [
                ClassLoader::class,
                ModuleLoader::class,
                PluginLoader::class,
            ],
            'lookups' => [
                Psr0Lookup::class,
                Psr4Lookup::class,
                ClassMapLookup::class,
            ],
        ];
    }

    abstract public function loadClass(string $className): bool;
    abstract public function lookupClass(string $className): ?string;
}
