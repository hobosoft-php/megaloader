<?php

namespace Hobosoft\MegaLoader;

use Hobosoft\MegaLoader\Contracts\LoaderInterface;
use Hobosoft\Config\Contracts\ConfigInterface;
use Psr\Log\LoggerInterface;

include_once(__DIR__ . '/Contracts/LoaderInterface.php');
include_once(__DIR__ . '/Contracts/LocatorInterface.php');

class MiniLoader implements LoaderInterface
{
    const string CLASSNAME = __CLASS__;

    private static bool $registered = false;
    private static self $instance;
    protected MiniConfig $config;
    protected MiniLogger $logger;

    public static function create(string $rootPath = null, array $config = [], bool $prepend = false): mixed
    {
        if(isset(self::$instance)) {
            if(class_exists(\Hobosoft\MegaLoader\MegaLoader::class)) {
                return \Hobosoft\MegaLoader\MegaLoader::create(self::$instance);
            }
            return self::$instance;
        }
        return (self::$instance = new self($rootPath ?? ROOTPATH, $config, $prepend));
    }

    protected function __construct(
        protected string $rootPath,
        protected array $initialConfig = [],
        protected bool $prepend = false,
    )
    {
        if(self::$registered === true) {
            throw new \Exception("MiniLoader already created.");
        }

        //if(isset($this->initialConfig['megaloader']) === false || isset($this->initialConfig['megaloader']['psr-4']) === false) {
        //    throw new \InvalidArgumentException('The configuration key "megaloader.psr-4" is required for bootstrapping, please define your application namespace.');
        //}

        $this->initialConfig['megaloader'] ??= [];
        $this->initialConfig['megaloader']['psr-4'] ??= [];
        $this->initialConfig['megaloader']['psr-4']['Hobosoft\\MegaLoader\\Tests\\'] = dirname(__DIR__).'/tests/';
        $this->initialConfig['megaloader']['psr-4']['Hobosoft\\MegaLoader\\'] = dirname(__DIR__).'/src/';

        $this->register();
        //class_exists('Hobosoft\MegaLoader\MiniConfig');
        //class_exists('Hobosoft\MegaLoader\MiniLogger');
        $this->config = new MiniConfig($this->initialConfig);
        $this->logger = new MiniLogger('miniloader');
        $this->logger->info("MiniLoader registered ok");
    }

    public function __destruct()
    {
        if(self::$registered === true) {
            $this->unregister();
        }
    }

    public function register(): void
    {
        if(self::$registered === true) {
            throw new \Exception("MiniLoader already registered");
        }
        spl_autoload_register([$this, 'loadClass'], true, $this->prepend);
        self::$registered = true;
    }

    public function unregister(): void
    {
        if(self::$registered === false) {
            throw new \Exception("MiniLoader not registered");
        }
        spl_autoload_unregister([$this, 'loadClass']);
        $this->logger->info("MiniLoader unregistered ok");
        self::$registered = false;
    }

    public function getRootPath(): string
    {
        return $this->rootPath;
    }

    public function getConfig(): MiniConfig
    {
        return $this->config;
    }

    public function getLogger(): MiniLogger
    {
        return $this->logger;
    }

    public function setConfig(ConfigInterface $config): void
    {
        $this->config->setDecoratedObject($config);
    }

    public function setLogger(LoggerInterface $logger): void
    {
        $this->logger->setDecoratedObject($logger);
    }

    public function load(string $name, string $type = 'class'): bool
    {
        if($type !== 'class') {
            throw new \Exception("MiniLoader only supports loading classes.");
        }
        return $this->loadClass($name);
    }

    protected function loadClass(string $className): bool
    {
        if(isset($this->initialConfig['megaloader']['psr-4']) === false) {
            die("MiniLoader has no namespace psr-4 configuration!");
        }
        $cfg = isset($this->config) ? $this->config['megaloader'] : $this->initialConfig['megaloader'];
        foreach ($cfg['psr-4'] as $k => $v) {
            if (($filename = $this->makeFilename($this->rootPath, $className, $k, $v)) !== false) {
                if(isset($this->logger)) {
                    $this->logger->info("   MiniLoader Computed filename '$filename' was good.");
                }
                $this->loadedClasses[$className] = $filename;
                require_once $filename;
                return true;
            }
        }
        $this->missingClasses[$className] = true;
        if(isset($this->logger)) {
            $this->logger->info("MiniLoader Failed to load class '$className'");
        }
        return false;
    }

    protected function makeFilename(string $basePath, string $className, string $search, string|array $replace): string|bool
    {
        if (str_starts_with($className, $search)) {
            $replace = is_string($replace) ? [$replace] : $replace;
            $tail = substr(strtr($className, '\\/', DIRECTORY_SEPARATOR), strlen($search)) . '.php';
            while (!empty($replace)) {
                $entry = rtrim(array_shift($replace), DIRECTORY_SEPARATOR);
                if($entry[0] === '/' || $entry[1] === ':') {
                    $fn = $entry . DIRECTORY_SEPARATOR . $tail;
                    if (file_exists($fn)) {
                        return $fn;
                    }
                }
                $root = ($entry[0] === '/') ? $entry : ($basePath . DIRECTORY_SEPARATOR . $entry);
                if (file_exists(($fn = ($root . DIRECTORY_SEPARATOR . $tail)))) {
                    return $fn;
                }
            }
        }
        return false;
    }
}
