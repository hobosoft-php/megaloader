<?php

namespace Hobosoft\MegaLoader;

use Closure;
use Hobosoft\MegaLoader\Contracts\ClassLoaderInterface;
use Hobosoft\MegaLoader\Contracts\ClassLookupInterface;
use Hobosoft\MegaLoader\Contracts\ManagerInterface;
use Hobosoft\MegaLoader\Loaders\CacheLoader;
use Hobosoft\MegaLoader\Loaders\ClassLoader;
use Hobosoft\MegaLoader\Loaders\LoaderAggregate;
use Hobosoft\MegaLoader\Loaders\Psr0Lookup;
use Hobosoft\MegaLoader\Loaders\Psr4Lookup;
use Psr\Log\LoggerInterface as PsrLoggerInterface;
use Psr\Log\NullLogger;
use Psr\SimpleCache\CacheInterface;

include_once(__DIR__ . '/Contracts/ClassLoaderInterface.php');
include_once(__DIR__ . '/Contracts/ClassLookupInterface.php');
include_once(__DIR__ . '/Contracts/MetadataInterface.php');
include_once(__DIR__ . '/Configuration.php');
include_once(__DIR__ . '/TinyLogger.php');
include_once(__DIR__ . '/Loaders/AbstractLoader.php');

class MegaLoader implements ManagerInterface
{
    private array $includes = [
        'Configuration.php',
        'TinyLogger.php',
        'Contracts' => [
            'ClassLoaderInterface.php',
            'ClassLookupInterface.php',
            'ManagerInterface.php',
            'MetadataInterface.php',
        ],
        'Loaders' => [
            'AbstractLoader.php',
            'LoaderDecorator.php',
            'NullLoader.php',
            'ClassLoader.php',
            'PluginLoader.php',
            'ModuleLoader.php',
        ],
        'Lookups' => [
            'AbstractLookup.php',
            'CacheLookup.php',
            'Psr0Lookup.php',
            'Psr4Lookup.php',
            'ClassMapLookup.php',
            'ManualLookup.php',
            'FinderLookup.php',
        ],
    ];

    const string CONFIG_SECTION = 'megaloader';
    private string $basePath = __DIR__;

    protected array $loaders = [];
    protected array $lookups = [];
    protected ?CacheInterface $loaderCache = null;
    protected ?CacheInterface $lookupCache = null;

    public function __construct(
        protected PsrLoggerInterface    $logger,
        protected array                 $config = [],
        protected mixed                 $loader = null,
    )
    {
        $this->logger = $logger ?? new NullLogger();
        foreach(Configuration::getConfigDefault() as $k => $v) {
            $this->config[$k] = $v;
        }
        spl_autoload_register([$this, 'loadClass'], true, true);
        if(is_null($loader) === false) {
            $this->lookups[] = $loader;
        }

        foreach($this->config['lookups'] as $lookup) {
            $resolved = match(true) {
                $lookup instanceof ClassLookupInterface => $lookup,
                is_string($lookup) => new ($lookup)($this, $logger),
                default => throw new \InvalidArgumentException("Unknown lookup type '{$lookup}'"),
            };
            $this->lookups[] = static fn(string $className) => $resolved->lookupClass($className);
        }

        $this->loader = new CacheLoader($logger, $config, new LoaderAggregate($config['loaders'] ?? [Psr4Lookup::class, Psr0Lookup::class], $logger, $config, null));
    }
    
    public function __destruct()
    {
        spl_autoload_unregister([$this, 'loadClass']);
        foreach($this->notLoaded as $className) {
            $this->logger->error("Error loading class '$className'.");
        }
    }

    public function addLookup(ClassLookupInterface|Closure|string $lookup): void
    {
        $resolved = match(true) {
            $lookup instanceof ClassLookupInterface => $lookup,
            is_string($lookup) => new ($lookup)($this, $this->logger),
            default => throw new \InvalidArgumentException("Unknown lookup type '{$lookup}'"),
        };
        $this->lookups[] = static fn(string $className) => $resolved->lookupClass($className);
    }

    public function addLoader(ClassLoaderInterface|Closure|string $loader): void
    {
        $resolved = match(true) {
            $loader instanceof ClassLoaderInterface => static fn(string $name) => $loader->lookupClass($name), //$loader,
            $loader instanceof Closure => $loader,
            is_string($loader) => ($ld = new ($loader)($this, $this->logger)),
            default => throw new \InvalidArgumentException("Unknown lookup type '{$loader}'"),
        };
        $this->lookups[] = static fn(string $name) => $resolved->lookupClass($name);
    }

    public function getLogger(string $string): PsrLoggerInterface
    {
        return $this->logger;
    }

    public function getConfig(string $string): mixed
    {
        return ($this->config[$string] ?? null);
    }

    public function lookupClass(string $className): ?string
    {
        $this->logger->info(__METHOD__ . ": Looking up class: {$className}");
        if(isset($this->loader) === true && ($ret = $this->loader->lookupClass($className)) !== null) {
            return $ret;
        }
        if($this->fallback !== null && ($ret = $this->fallback->lookupClass($className)) === null) {
            return $ret;
        }
        $this->notLoaded[] = $className;
        return null;
    }

    public function loadClass(string $className): bool
    {
        // TODO: Implement loadClass() method.
    }

    public function setConfig(mixed $config): void
    {
        // TODO: Implement setConfig() method.
    }
}
