<?php

namespace Hobosoft\MegaLoader;

use Closure;
use Hobosoft\Boot\Boot;
use Hobosoft\Config\Contracts\ConfigInterface;
use Hobosoft\MegaLoader\Contracts\LoaderInterface;
use Hobosoft\MegaLoader\Contracts\LocatorInterface;
use Hobosoft\MegaLoader\Loaders\CacheLoader;
use Hobosoft\MegaLoader\Loaders\ClassLoader;
use Hobosoft\MegaLoader\Loaders\LoaderDelegator;
use Hobosoft\MegaLoader\Loaders\PluginLoader;
use Hobosoft\MegaLoader\Locators\ClassMapLocator;
use Hobosoft\MegaLoader\Locators\PluginLocator;
use Hobosoft\MegaLoader\Locators\Psr0Locator;
use Hobosoft\MegaLoader\Locators\Psr4Locator;
use Psr\Log\LoggerInterface as PsrLoggerInterface;

class MegaLoader
{
    const string CONFIG_SECTION = 'megaloader';
    const string CACHE_SECTION = 'megaloader-' . PHP_SAPI;

    protected mixed $loader;

    public function __construct(
        private PsrLoggerInterface $logger,
        private ConfigInterface    $config,
    )
    {
        Boot::include(__DIR__ . '/Loaders/AbstractLoader.php');
        Boot::include(__DIR__ . '/Loaders/ClassLoader.php');
        Boot::include(__DIR__ . '/Locators/AbstractLocator.php');
        Boot::include(__DIR__ . '/Locators/Psr4Locator.php');
        $this->config[self::CONFIG_SECTION] = Configuration::process($this->config[self::CONFIG_SECTION]);
        spl_autoload_register([$this, 'loadClass'], true, true);
        $this->loader = new LoaderDelegator($logger, $this->config, [
            'class' => static fn() => new ClassLoader($logger, $config, [
                ClassMapLocator::class,
                Psr4Locator::class,
                Psr0Locator::class,
            ]),
            'plugin' => static fn() => new PluginLoader($logger, $config, [
                PluginLocator::class
            ]),
        ]);
        if((($this->config['cache'] ?? [])['enabled'] ?? false) === true) {
            $this->loader = new CacheLoader($logger, $config, $this->loader);
        }
    }
    
    public function __destruct()
    {
        spl_autoload_unregister([$this, 'loadClass']);
    }

    public function registerLoader(\Closure|LoaderInterface|string $loader, string $type = 'class'): void
    {
        $this->loader->registerLoader($loader, $type);
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
