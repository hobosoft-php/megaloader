<?php

namespace Hobosoft\MegaLoader\Loaders;

use Hobosoft\Boot\Paths;
use Hobosoft\MegaLoader\Composer\Composer;
use Hobosoft\MegaLoader\Contracts\LoaderInterface;
use Hobosoft\MegaLoader\Contracts\LocatorInterface;
use Hobosoft\MegaLoader\MegaLoader;
use Hobosoft\MegaLoader\Utils;
use Psr\Log\LoggerInterface as PsrLoggerInterface;

class CacheLoader extends LoaderDecorator implements LocatorInterface
{
    const string TYPE = 'class';
    const string CLASSMAP_FILENAME = 'classmap.inc';

    protected string $path;
    private array $classMap = [];
    private array $missingMap = [];
    private Composer $composer;

    public function __construct(
        MegaLoader                             $parent,
        LoaderInterface                        $decoatedLoader,
    )
    {
        parent::__construct($parent, null, $decoatedLoader);
        $this->path = $this->getConfig()['megaloader.cache.path'];
        if (!is_dir($this->path) && !mkdir($this->path, 0777, true) && !is_dir($this->path)) {
            throw new \RuntimeException(sprintf('Directory "%s" was not created', $this->path));
        }
        //$this->composer = new Composer($this->getLogger(), ROOTPATH);
        //$this->readComposerAutoload(ROOTPATH . '/composer.json');
        $this->readCacheFile(Paths::join($this->path, self::CLASSMAP_FILENAME));
    }

    public function __destruct()
    {
        $this->writeCacheFile(Paths::join($this->path, self::CLASSMAP_FILENAME));
    }

    public function locate(string $name): string|bool
    {
        print(__METHOD__ . ":  locating '$name'.\n");
        if(array_key_exists($name, $this->classMap)) {
            print(__METHOD__ . ":  Found classMap entry for '$name'.\n");
            return $this->classMap[$name];
        }
        if(($ret = $this->decoratedLoader->locate($name)) !== false) {
            print(__METHOD__ . ":  Located class for '$name' in file $ret.\n");
            return ($this->classMap[$name] = $ret);
        }
        return false;
    }

    public function load(string $name): bool
    {
        print(__METHOD__ . ":  loading '$name'.\n");
        if(($located = $this->locate($name)) !== false) {
            return Utils::include($located);
        }
        return ($this->missingMap[$name] = false);
    }

    private function writeCacheFile(string $filename): void
    {
        file_exists($filename) && unlink($filename);
        ksort($this->classMap);
        file_put_contents($filename, "<?php\n\nreturn " . var_export($this->classMap, true) . ";");
    }

    private function readCacheFile(string $filename): void
    {
        if(is_file($filename)) {
            $this->classMap = include($filename);
        }
    }
}
