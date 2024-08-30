<?php

namespace Hobosoft\MegaLoader;

use Closure;
use Hobosoft\MegaLoader\Composer\Composer;
use Hobosoft\MegaLoader\Contracts\LoaderInterface;
use Hobosoft\MegaLoader\Contracts\LocatorInterface;
use Hobosoft\MegaLoader\Contracts\ResolvingLoaderInterface;
use Hobosoft\MegaLoader\Contracts\ResolvingLocatorInterface;
use Hobosoft\MegaLoader\Decorators\CacheLocatorDecorator;
use Hobosoft\MegaLoader\Loaders\ClassLoader;
use Hobosoft\MegaLoader\Traits\LocatorTraits;
use Hobosoft\MegaLoader\Traits\LoaderTraits;
use Hobosoft\MegaLoader\Traits\ResolverTraits;
use Psr\Log\LoggerInterface as PsrLoggerInterface;

class MegaLoader implements LoaderInterface
{
    //const string CONFIG_SECTION = 'megaloader';
    const string CACHE_SECTION = 'megaloader-' . PHP_SAPI;

    //private array $types = [];
    protected ResolvingLoaderInterface   $loaderResolver;
    protected ResolvingLocatorInterface  $locatorResolver;
    protected ?MiniLoader                $miniLoader = null;
    protected ?Composer                  $composer = null;
    private static ?string               $rootPath = null;

    public function __construct(
        protected ?MiniLogger         $logger = null,
        protected ?MiniConfig         $config = null,
        string                        $rootPath = null,
        mixed                         $miniLoader = null,
        bool                          $disableMiniLoader = true,
    )
    {
        self::$rootPath = $rootPath;

        Utils::includeArray([
            'Contracts/*',
            'Exceptions/*',
            'Traits/*',
            'Composer/*',
            'Locators/*',
            'Loaders/*',
            'Decorators/*',
        ], __DIR__, Utils::ALLOW_GLOB);

        $this->locatorResolver = new class($config) implements ResolvingLocatorInterface, LocatorInterface {
            use LocatorTraits, ResolverTraits;
            public function locate(string $name, mixed $type = null): string|bool
            {
                return $this->resolve($name, $type);
            }
        };

        $this->loaderResolver = new class($config, $this->locatorResolver) implements ResolvingLoaderInterface, LoaderInterface
        {
            use LoaderTraits, ResolverTraits;
        };

        foreach(($config['locators'] ?? []) as $type => $locators) {
            foreach($locators as $locator) {
                $this->locatorResolver->add(Type::fromString($type), $locator);
            }
        }

        foreach(($config['loaders'] ?? []) as $type => $loader) {
            $this->loaderResolver->add(Type::fromString($type), $loader);
        }

        foreach(($config['decorators'] ?? []) as $type => $decorators) {
            foreach($decorators as $decorator) {
                //$this->loaderResolver->add(Type::fromString($type), $loader);
            }
        }
        /*
        try {
            $this->config[self::CONFIG_SECTION] = Configuration::process($this->config[self::CONFIG_SECTION] ?? []);
        }
        catch (\Exception $e) {
            print("exception: ".$e->getMessage()."\n");
        }
        */
        if ($this->config['cache']['enabled'] === true) {
            $this->loaderResolver->decorate(Type::T_CLASS, ClassLoader::class, CacheLocatorDecorator::class);
            print("Cache enabled for megaloader.\n");
        }
        spl_autoload_register([$this, 'load'], true, $this->config['prepend'] ?? false);
        if(is_null($this->logger)) {
            $this->logger = new MiniLogger();
            $this->logger->info("Logger created!");
        }
        $this->getComposer();
        if(is_null(($this->miniLoader = $miniLoader)) === false) {
            if($disableMiniLoader) {
                $this->miniLoader->unregister();
            }
        }
    }

    public static function getRootPath()
    {
        if(is_null(self::$rootPath) === true) {
            return Utils::getRootPath();
        }
        return self::$rootPath;
    }

    public function __destruct()
    {
        spl_autoload_unregister([$this, 'load']);
    }

    public function getLogger(): MiniLogger
    {
        return $this->logger;
    }

    public function getConfig(): MiniConfig
    {
        return $this->config;
    }

    public function getComposer(): Composer
    {
        if(is_null($this->composer) === true) {
            $this->composer = new Composer($this->logger, MegaLoader::getRootPath());
            $cfg = $this->composer->loadAutoload(MegaLoader::getRootPath());
            $this->config->set($cfg);
            Utils::includeArray($cfg['files'] ?? []);
        }
        return $this->composer;
    }

    public function getLoaderResolver(): ResolvingLoaderInterface
    {
        return $this->loaderResolver;
    }

    public function getLocatorResolver(): ResolvingLocatorInterface
    {
        return $this->locatorResolver;
    }

    public function locate(string $name, string $type = 'class'): string|bool
    {
        return $this->locatorResolver->locate($name, $type);
    }

    public function load(string $name, string $type = 'class'): bool
    {
        return $this->loaderResolver->load($name, $type);
    }

    public function dump(): void
    {
        $this->loaderResolver->dump();
        $this->locatorResolver->dump();
    }
}
