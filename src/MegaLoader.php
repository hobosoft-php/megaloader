<?php

namespace Hobosoft\MegaLoader;

use Hobosoft\Config\Config;
use Hobosoft\Config\Contracts\ConfigInterface;
use Hobosoft\FileLoaders\Loader\DelegatingLoader;
use Hobosoft\FileLoaders\Loader\FileLocator;
use Hobosoft\MegaLoader\Contracts\LoaderInterface;
use Hobosoft\MegaLoader\Loaders\AbstractLoader;
use Hobosoft\MegaLoader\Loaders\CacheLoader;
use Hobosoft\MegaLoader\Loaders\ClassLoader;
use Hobosoft\MegaLoader\Loaders\LoaderAggregate;
use Hobosoft\MegaLoader\Loaders\LoaderDelegator;
use Hobosoft\MegaLoader\Loaders\LoaderTypes;
use Hobosoft\MegaLoader\Loaders\PluginLoader;
use Hobosoft\MegaLoader\Locators\FinderLocator;
use Hobosoft\MegaLoader\Locators\LocatorDelegator;
use Hobosoft\MegaLoader\Locators\MapLocator;
use Hobosoft\MegaLoader\Locators\PluginLocator;
use Hobosoft\MegaLoader\Locators\Psr0Locator;
use Hobosoft\MegaLoader\Locators\Psr4Locator;
use Hobosoft\Plugin\Manifest\Types\Loader;
use Psr\Log\LoggerInterface as PsrLoggerInterface;

class MegaLoader implements LoaderInterface
{
    const string CONFIG_SECTION = 'megaloader';
    const string CACHE_SECTION = 'megaloader-' . PHP_SAPI;

    private array $types = [];
    protected LoaderInterface $loader;

    public function __construct(
        private PsrLoggerInterface $logger,
        private ConfigInterface    $config,
    )
    {
        class_exists(ClassLoader::class, true);
        class_exists(MapLocator::class, true);
        class_exists(Psr4Locator::class, true);
        class_exists(Psr0Locator::class, true);
        class_exists(LocatorDelegator::class, true);
        class_exists(Utils::class, true);

        $this->types = include __DIR__ . '/Resource/config.php';
        $this->loader = new LoaderDelegator($this);
        $this->loaders = [];
        /**
         * @var AbstractLoader $loader
         * @var array $locators
         */
        foreach($this->types as $loader => $locators) {
            $type = constant($loader . '::TYPE');
            $locators = is_string($locators) ? [$locators] : $locators;
            $this->loader->register($loader, $locators, $type);
        }

        //$this->config[self::CONFIG_SECTION] = Configuration::process($this->config[self::CONFIG_SECTION]);
        $this->config->fromArray(Configuration::process($this->config[self::CONFIG_SECTION]));
        $config = new Config($logger, null, $this->config[self::CONFIG_SECTION]);

        spl_autoload_register([$this, 'load'], true, !false);
    }
    
    public function __destruct()
    {
        spl_autoload_unregister([$this, 'load']);
    }

    public function getLogger(): PsrLoggerInterface
    {
        return $this->logger;
    }

    public function getConfig(): ConfigInterface
    {
        return $this->config;
    }

    public function registerLoader(\Closure|LoaderInterface|string|array $loader, string $type = LoaderTypes::CLASS): void
    {
        $this->loader->register($loader, $type);
    }

    public function locate(string $name, string $type = 'class'): string|bool
    {
        return $this->loader->locate($name, $type);
    }

    public function load(string $name, string $type = 'class'): bool
    {
        return $this->loader->load($name, $type);
    }
}
