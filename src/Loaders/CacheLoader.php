<?php

namespace Hobosoft\MegaLoader\Loaders;

use Hobosoft\Config\Contracts\ConfigInterface;
use Hobosoft\MegaLoader\Contracts\LoaderInterface;
use Psr\Log\LoggerInterface as PsrLoggerInterface;

class CacheLoader extends AbstractLoader
{
    const string CLASSES_FILENAME = 'classes.inc';

    protected array $classes = [];
    protected string $path;

    public function __construct(
        PsrLoggerInterface      $logger,
        ConfigInterface         $config,
        private LoaderInterface $decorated,
    )
    {
        parent::__construct($logger, $config);
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

    public function locate(string $name): string|bool
    {
        if(array_key_exists($name, $this->classes)) {
            return $this->classes[$name];
        }
        if(($ret = $this->decorated->locate($name)) !== null) {
            return ($this->classes[$name] = $ret);
        }
        return false;
    }

    public function load(string $name): bool
    {
        return $this->decorated->load($this->locate($name));
    }
}
