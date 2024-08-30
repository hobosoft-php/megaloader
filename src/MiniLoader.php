<?php

namespace Hobosoft\MegaLoader;

class MiniLoader
{
    private static function getRootPath()
    {
        return dirname(__DIR__);
    }

    public function createMegaLoader(string $rootPath = null): mixed
    {
        return (fn($log, $cfg, $root, $mega) => (new MegaLoader($log, $cfg, $root, $mega, true)))($this->logger, $this->config, $rootPath, $this);
    }

    public function __construct(
        protected ?MiniLogger $logger = null,
        protected MiniConfig|array $config = [],
        protected bool $prepend = false,
    )
    {
        $this->register();
        class_exists('Hobosoft\MegaLoader\Type');
        class_exists('Hobosoft\MegaLoader\Utils');
        class_exists('Hobosoft\MegaLoader\MiniConfig');
        //class_exists('Hobosoft\MegaLoader\MiniLogger');
        $this->config = is_array($config) ? new MiniConfig($config) : $config;
    }

    public function __destruct()
    {
        $this->unregister();
    }

    public function register(): void
    {
        spl_autoload_register([$this, 'loadClass'], true, $this->prepend);
        print("MiniLoader registered ok\n");
    }

    public function unregister(): void
    {
        spl_autoload_unregister([$this, 'loadClass']);
        print("MiniLoader unregistered ok\n");
    }

    public function getConfig(): array
    {
        return $this->config;
    }

    protected function loadClass(string $className): void
    {
        print("MiniLoader attempting to load class '$className'...\n");
        foreach ($this->config['psr-4'] as $k => $v) {
            if (($filename = $this->makeFilename(self::getRootPath(), $className, $k, $v)) !== false) {
                $this->loadedClasses[$className] = $filename;
                require_once $filename;
                return;
            }
        }
        $this->missingClasses[$className] = true;
        print("Failed to load class '$className'\n");
    }

    protected function makeFilename(string $basePath, string $className, string $search, string|array $replace): string|bool
    {

        if (str_starts_with($className, $search)) {
            $replace = is_string($replace) ? [$replace] : $replace;
            $tail = substr(strtr($className, '\\/', DIRECTORY_SEPARATOR), strlen($search)) . '.php';
            while (!empty($replace)) {
                $entry = array_shift($replace);
                $root = ($entry[0] === '/') ? $entry : ($basePath . DIRECTORY_SEPARATOR . $entry);
                if (file_exists(($fn = ($root . DIRECTORY_SEPARATOR . $tail)))) {
                    return $fn;
                }
            }
        }
        return false;
    }
}
