<?php

namespace Hobosoft\MegaLoader;

use Closure;
use Hobosoft\MegaLoader\Composer\Composer;
use Hobosoft\MegaLoader\Contracts\LoaderInterface;
use Hobosoft\MegaLoader\Contracts\LocatorInterface;
use Hobosoft\MegaLoader\Contracts\ResolvingLoaderInterface;
use Hobosoft\MegaLoader\Contracts\ResolvingLocatorInterface;
use Hobosoft\MegaLoader\Traits\LocatorTraits;
use Hobosoft\MegaLoader\Traits\LoaderTraits;
use Hobosoft\MegaLoader\Traits\ResolverTraits;

class MegaLoader //implements LoaderInterface
{
    const string CACHE_SECTION = 'megaloader-' . PHP_SAPI;

    public static array $loadedClasses = [];
    public static array $missingClasses = [];
    private static self                  $instance;
    public static array                  $pluginPsr4 = [];
    protected string                     $rootPath;
    protected MiniLogger                 $logger;
    protected MiniConfig                 $config;
    protected Composer                   $composer;
    protected ResolvingLoaderInterface   $loaderResolver;
    protected ResolvingLocatorInterface  $locatorResolver;
    private static mixed                 $composerLoader = null;

    public static function setComposerClassLoader(mixed $obj): void
    {
        self::$composerLoader = $obj;
    }

    public static function getComposerLoader(): mixed
    {
        return self::$composerLoader;
    }

    public static function create(MiniLoader|string|null $arg = null): self
    {
        $configFile = ROOTPATH . '/config/megaloader.php';
        if(isset(self::$instance)) {
            return self::$instance;
        }
        if(!($arg instanceof MiniLoader)) {
            if($arg === null && defined('ROOTPATH') === false) {
                throw new \Exception("The rootPath parameter must be set if the constant 'ROOTPATH' is not defined.");
            }
            $arg = MiniLoader::create($arg ?? ROOTPATH, file_exists($configFile) ? ([ 'megaloader' => include($configFile)]): []);
        }
        $ret = (self::$instance = new self($arg));
        //$ret->setConfig([ 'megaloader' => include($configFile)]);
        return $ret;
    }
    
    public static function addConfig(string $section, array $cfg)
    {
        $old = (self::$instance->getConfig())[$section];
        foreach($cfg as $k => $v) {
            $old[$k] = $v;
        }
        self::$instance->getConfig()->set($section, $old);
        //$oldcfg = (self::$instance)->config->get();
        //$oldcfg = array_merge($oldcfg, $cfg);
        //(self::$instance)->config->set($oldcfg);
    }

    /**
     * @throws \Exception
     */
    protected function __construct(MiniLoader $miniLoader)
    {
        $this->rootPath = $miniLoader->getRootPath();

        $this->logger = $miniLoader->getLogger();
        $this->config = $miniLoader->getConfig();

        $configFile = ROOTPATH . '/config/megaloader.php';
        $ld = include($configFile);
        $this->config->merge($ld);

        $this->locatorResolver = new class($this->config, $this->logger) implements ResolvingLocatorInterface, LocatorInterface {
            use LocatorTraits, ResolverTraits;
            public function locate(string $name, mixed $type = null): array|string|bool {
                return $this->resolve($name, $type);
            }
        };

        $this->loaderResolver = new class($this->config, $this->logger, $this->locatorResolver) implements ResolvingLoaderInterface, LoaderInterface {
            use LoaderTraits, ResolverTraits;
        };

        $section = $this->config['megaloader'];
        foreach(($section['locators'] ?? []) as $type => $locators) {
            foreach($locators as $locator) {
                $this->locatorResolver->add(Type::fromString($type), $locator);
            }
        }

        foreach(($section['loaders'] ?? []) as $type => $loader) {
            $this->loaderResolver->add(Type::fromString($type), $loader);
        }

        /*
        try {
            $this->config[self::CONFIG_SECTION] = Configuration::process($this->config[self::CONFIG_SECTION] ?? []);
        }
        catch (\Exception $e) {
            $this->logger->info("exception: ".$e->getMessage()."\n");
        }

        if ((($this->config['cache'] ?? []) ['enabled']) ?? null === true) {
            $this->loaderResolver->decorate(Type::T_CLASS, ClassLoader::class, CacheLocatorDecorator::class);
            $this->logger->info("Cache enabled for megaloader.");
        }*/
        spl_autoload_register([$this, 'load'], true, $this->config['megaloader.prepend'] ?? false);

        //make the miniloader destruct
        $miniLoader->unregister();
        $miniLoader = null; unset($miniLoader);

        $this->getComposer();
    }

    public function __destruct()
    {
        spl_autoload_unregister([$this, 'load']);
    }

    public function setConfig(/*ConfigInterface*/mixed $config): void
    {
        $this->config->setConfig($config);
    }

    public function setLogger(/*LoggerInterface*/mixed $logger): void
    {
        $this->logger->setLogger($logger);
    }

    public function getConfig(): MiniConfig
    {
        return $this->config;
    }

    public function getLogger(): MiniLogger
    {
        return $this->logger;
    }

    public function getComposer(): Composer
    {
        $this->logger->info("megaloader: checking composer...");
        if(isset($this->composer) === false) {
            $this->logger->info("loading composer from'".ROOTPATH."'.");
            $this->composer = new Composer($this->logger, ROOTPATH);
            $cfg = $this->composer->loadAutoload(ROOTPATH);
            $this->config->merge(['megaloader' => $cfg]);
            $dbgPath = ROOTPATH.'/var/debug-'.PHP_SAPI;
            if(is_dir($dbgPath) === false) {
                @mkdir($dbgPath, 0777, true);
            }
            file_put_contents($dbgPath.'/included.txt', implode(PHP_EOL, get_included_files()));
            file_put_contents($dbgPath.'/composer_files.txt', implode(PHP_EOL, $cfg['files']));
            try {
                Utils::includeArray($cfg['files'] ?? []);
            }
            catch(\Exception $e) {
                $this->logger->info("Composer autoload errors: ".$e->getMessage());
            }
            $this->logger->info("Composer autoload completed.");
        }
        if(is_null($this->composer) === true) {
            $this->logger->info("megaloader: NO composer!!!!!");
        }
        return $this->composer;
    }

    public function addLocator(string $className, Type $type = Type::T_CLASS, Closure $closure = null): void
    {
        if(is_null($closure) === false) {
            $this->locatorResolver->add($type, $className, $closure);
        }
        $this->locatorResolver->add($type, $className, $closure);
    }

    public function addLoader(string $className, Type $type = Type::T_CLASS, Closure $closure = null): void
    {
        $this->loaderResolver->add($type, $className);
    }

    public function locate(string $name, Type|string $type = 'class'): array|string|bool
    {
        $this->logger->info("megaloader: locating '$name'...");
        return $this->locatorResolver->locate($name, $type);
    }

    public function load(string $name, Type|string $type = 'class'): bool
    {
        $this->logger->info("megaloader: loading '$name'...");
        return $this->loaderResolver->load($name, $type);
    }

    public function dump(): void
    {
        $this->loaderResolver->dump();
        $this->locatorResolver->dump();
    }
}
