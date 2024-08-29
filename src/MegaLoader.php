<?php

namespace Hobosoft\MegaLoader;

use Hobosoft\Boot\Paths;
use Hobosoft\Config\Config;
use Hobosoft\Config\Contracts\ConfigInterface;
use Hobosoft\FileLoaders\Loader\DelegatingLoader;
use Hobosoft\FileLoaders\Loader\FileLocator;
use Hobosoft\MegaLoader\Contracts\LoaderInterface;
use Hobosoft\MegaLoader\Loaders\AbstractLoader;
use Hobosoft\MegaLoader\Loaders\CacheLoader;
use Hobosoft\MegaLoader\Loaders\ClassLoader;
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

include __DIR__.'/Utils.php';
Utils::include([
    Utils::fullPathGlob('Contracts/*.php', __DIR__),
    Utils::fullPathGlob('Exceptions/*.php', __DIR__),
    'Composer/Composer.php',
    'Composer/ComposerJson.php',
    'Loaders/AbstractLoader.php',
    'Loaders/LoaderTypes.php',
    'Loaders/LoaderDelegator.php',
    'Loaders/LoaderDecorator.php',
    'Loaders/ClassLoader.php',
    'Loaders/CacheLoader.php',
    'Loaders/PluginLoader.php',
    'Locators/AbstractLocator.php',
    'Locators/LocatorDelegator.php',
    'Locators/ComposerLocator.php',
    'Locators/MapLocator.php',
    'Locators/Psr0Locator.php',
    'Locators/Psr4Locator.php',
    'Locators/FinderLocator.php',
    'Configuration.php',
], __DIR__);

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
/*        include 'Utils.php';
        $preload = [
            Utils::fullPathGlob('Contracts/*.php', __DIR__),
            Utils::fullPathGlob('Exceptions/*.php', __DIR__),
            'Composer/Composer.php',
            'Composer/ComposerJson.php',
            'Loaders/AbstractLoader.php',
            'Loaders/LoaderTypes.php',
            'Loaders/LoaderDelegator.php',
            'Loaders/LoaderDecorator.php',
            'Loaders/ClassLoader.php',
            'Loaders/CacheLoader.php',
            'Loaders/PluginLoader.php',
            'Locators/AbstractLocator.php',
            'Locators/LocatorDelegator.php',
            'Locators/ComposerLocator.php',
            'Locators/MapLocator.php',
            'Locators/Psr0Locator.php',
            'Locators/Psr4Locator.php',
            'Locators/FinderLocator.php',
            'Configuration.php',
        ];
        array_walk($preload, function ($fn, $key, $dir) {
            if (is_string($fn) && Paths::isAbsolute($fn) === false) {
                $fn = Paths::join($dir, $fn);
            }
            Utils::include($fn);
        }, __DIR__);*/

        $this->types = include __DIR__ . '/Resource/config.php';
        $this->loader = new LoaderDelegator($this);
        foreach ($this->types as $loader => $locators) {
            $type = constant($loader . '::TYPE');
            $locators = is_string($locators) ? [$locators] : $locators;
            print("Registered loader '$loader' as type '$type', locators: " . implode(', ', $locators) . "\n");
            $this->loader->register($loader, $locators, $type);
        }
        $this->config[self::CONFIG_SECTION] = Configuration::process($this->config[self::CONFIG_SECTION] ?? []);
        if ($this->config[self::CONFIG_SECTION . '.cache.enabled'] === true) {
            $cacheLoader = static fn($th) => new CacheLoader($th, $this->loader->getLoaderByType('class'));
            $this->loader->replaceType('class', $cacheLoader);
            print("Cache enabled for megaloader.\n");
        }
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

    public static function defineLoader(string $classname): \Closure
    {
        return static fn($th) => Utils::validateInstance(new $classname($th), LoaderInterface::class);
    }

    public static function defineLocator(string $classname): \Closure
    {
        return static fn($th) => new $classname($th);
    }

    public static function defineDelegator(string $classname): \Closure
    {
        return static fn($th) => new $classname($th);
    }

    public static function defineDecorator(string $classname): \Closure
    {
        return static fn($th) => new $classname($th);
    }

    public function register(\Closure|LoaderInterface|string|array $loader, string $type = LoaderTypes::CLASSES->name): void
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
