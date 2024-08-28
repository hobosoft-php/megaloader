<?php

namespace Hobosoft\MegaLoader\Loaders;

use Hobosoft\MegaLoader\Contracts\LoaderInterface;
use Hobosoft\MegaLoader\Contracts\LocatorInterface;
use Hobosoft\MegaLoader\MegaLoader;
use Psr\Log\LoggerInterface as PsrLoggerInterface;

class CacheLoader extends LoaderDecorator
{
    const string CLASSES_FILENAME = 'classes.inc';

    protected array $classes = [];
    protected string $path;

    public function __construct(
        MegaLoader                             $parent,
        \Closure|LocatorInterface|string|array $locator,
        private LoaderInterface                $decorated,
    )
    {
        parent::__construct($parent, $locator);
        $this->path = $this->getConfig()['cache']['path'];
        if (!is_dir($this->path) && !mkdir($this->path, 0777, true) && !is_dir($this->path)) {
            throw new \RuntimeException(sprintf('Directory "%s" was not created', $this->path));
        }
        //$this->classes = file_exists(($fn = $this->path.'/classes.inc')) ? include $fn : [];
    }

    public function __destruct()
    {
        $head = "<?php\n\nreturn ";
        $tail = ";";
        if(file_exists(($fn = $this->path.'/classes.inc'))) {
            unlink($fn);
        }
        ksort($this->classes);
        file_put_contents($fn, $head . var_export($this->classes, true) . $tail);
    }

    public function locate(string $name): string|bool
    {
        return $this->decorated->locate($name);
    }

    public function load(string $name): bool
    {
        return $this->decorated->load($name);
    }
}
