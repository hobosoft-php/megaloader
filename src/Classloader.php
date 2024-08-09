<?php

namespace Library\Classloader;

use Library\Classloader\Contracts\ClassLoaderInterface;
use Library\Classloader\Loaders\AbstractLoader;
use Library\Classloader\Loaders\CacheLoader;
use Library\Classloader\Loaders\LoaderDecorator;
use Library\Classloader\Loaders\LoaderGroup;
use Library\Classloader\Loaders\LoaderWrapper;
use Library\Classloader\Loaders\Psr0Loader;
use Library\Classloader\Loaders\Psr4Loader;
use Psr\Log\LoggerInterface as PsrLoggerInterface;

include_once(__DIR__ . '/Contracts/ClassLoaderInterface.php');
include_once(__DIR__ . '/Contracts/ClassLookupInterface.php');
include_once(__DIR__ . '/Contracts/MetadataInterface.php');

include_once(__DIR__ . '/Configuration.php');
include_once(__DIR__ . '/TinyLogger.php');

include_once(__DIR__ . '/Loaders/AbstractLoader.php');
include_once(__DIR__ . '/Loaders/Psr0Loader.php');
include_once(__DIR__ . '/Loaders/Psr4Loader.php');

class Classloader extends AbstractLoader
{
    const string CONFIG_SECTION = 'classloader';

    protected ClassLoaderInterface $loader;
    protected array $notLoaded = [];

    public function __construct(
        ?PsrLoggerInterface $logger,
        array               $config = [],
        protected mixed     $oldLoader = null,
    )
    {
        $this->logger = $logger ?? new TinyLogger();
        $this->fallback = ($oldLoader instanceof ClassLoaderInterface) ? $oldLoader : new LoaderDecorator($oldLoader, $logger, $config);
        foreach(Configuration::getConfigDefault() as $k => $v) {
            $config[$k] = $v;
        }
        parent::__construct($this->logger, $config);
        spl_autoload_register([$this, 'loadClass'], true, true);
        $this->loader = new CacheLoader($logger, $config, new LoaderGroup($config['loaders'] ?? [Psr4Loader::class, Psr0Loader::class], $logger, $config, null));
    }
    
    public function __destruct()
    {
        spl_autoload_unregister([$this, 'loadClass']);
        foreach($this->notLoaded as $className) {
            $this->logger->error("Error loading class '$className'.");
        }
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
}
