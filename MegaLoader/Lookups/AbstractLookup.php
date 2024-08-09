<?php

namespace Hobosoft\MegaLoader\Lookups;

use Hobosoft\MegaLoader\Contracts\ClassLookupInterface;
use Hobosoft\MegaLoader\Contracts\ManagerInterface;
use Hobosoft\MegaLoader\Loaders\ClassMapLookup;
use Hobosoft\MegaLoader\Loaders\Psr0Lookup;
use Hobosoft\MegaLoader\Loaders\Psr4Lookup;
use Psr\Log\LoggerInterface as PsrLoggerInterface;

abstract class AbstractLookup implements ClassLookupInterface
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
                Psr4Lookup::class,
                ClassMapLookup::class,
            ],
            'lookups' => [
                Psr0Lookup::class,
                Psr4Lookup::class,
                ClassMapLookup::class,
            ],
        ];
    }

    public function lookupClass(string $className): ?string
    {
        // TODO: Implement lookupClass() method.
    }
}