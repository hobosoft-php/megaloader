<?php

namespace Library\Classloader\Loaders;

use Library\Classloader\Contracts\ClassLoaderInterface;
use Psr\Log\LoggerInterface as PsrLoggerInterface;

class CacheLoader extends AbstractLoader
{
    const string CLASSES_FILENAME = 'classes.inc';

    protected array $classes = [];
    protected string $path;

    public function __construct(
        ?PsrLoggerInterface   $logger,
        ?array                $config = null,
        ?ClassLoaderInterface $decorated = null,
    )
    {
        parent::__construct($logger, $config, $decorated);
        $this->path = $config['cache']['path'];
        if (!is_dir($this->path) && !mkdir($this->path, 0777, true) && !is_dir($this->path)) {
            throw new \RuntimeException(sprintf('Directory "%s" was not created', $this->path));
        }
        $this->classes = file_exists(($fn = $this->path.'/classes.inc')) ? include $fn : [];
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

    public function __call(string $name, array $arguments): mixed
    {
        if(method_exists($this->fallback, $name)) {
            return call_user_func_array([$this->fallback, $name], $arguments);
        }
        return null;
    }

    public function lookupClass(string $className): ?string
    {
        if(array_key_exists($className, $this->classes)) {
            return $this->classes[$className];
        }
        if(($ret = $this->fallback->lookupClass($className)) !== null) {
            return ($this->classes[$className] = $ret);
        }
        return null;
    }
}
