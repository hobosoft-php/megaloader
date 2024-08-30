<?php

namespace Hobosoft\MegaLoader\Decorators;

use Hobosoft\MegaLoader\Contracts\LocatorInterface;
use Hobosoft\MegaLoader\Contracts\ResolvingLocatorInterface;
use Hobosoft\MegaLoader\MegaLoader;
use Hobosoft\MegaLoader\Utils;

class CacheLocatorDecorator implements LocatorInterface
{
    const string TYPE = 'class';
    const string CLASSMAP_FILENAME = 'classmap.inc';

    protected string $path;
    private array $classMap = [];
    private array $missingMap = [];

    public function __construct(
        protected ResolvingLocatorInterface $locatorResolver,
        protected LocatorInterface          $decoratedLocator,
    )
    {
        $this->path = $this->locatorResolver->getConfig()['cache']['path'];
        if (!is_dir($this->path) && !mkdir($this->path, 0777, true) && !is_dir($this->path)) {
            throw new \RuntimeException(sprintf('Directory "%s" was not created', $this->path));
        }
        $this->readCacheFile(Utils::joinPaths($this->path, self::CLASSMAP_FILENAME));
    }

    public function __destruct()
    {
        $this->writeCacheFile(Utils::joinPaths($this->path, self::CLASSMAP_FILENAME));
    }

    private function writeCacheFile(string $filename): void
    {
        file_exists($filename) && unlink($filename);
        ksort($this->classMap);
        file_put_contents($filename, "<?php\n\nreturn " . var_export($this->classMap, true) . ";");
    }

    private function readCacheFile(string $filename): void
    {
        if (is_file($filename)) {
            $this->classMap = include($filename);
        }
    }

    public function locate(string $name): string|bool
    {
        if (array_key_exists($name, $this->classMap)) {
            print(__METHOD__ . ":  Found classMap entry for '$name'.\n");
            return $this->classMap[$name];
        }
        if (($ret = $this->decoratedLocator->locate($name)) !== false) {
            print(__METHOD__ . ":  Located class for '$name' in file $ret.\n");
            return ($this->classMap[$name] = $ret);
        }
        print(__METHOD__ . ":  failed locating '$name'.\n");
        $this->missingMap[$name] = true;
        return false;
    }
}
