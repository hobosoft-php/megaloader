<?php

namespace Hobosoft\MegaLoader\Composer;

use Hobosoft\Finders\FileFinder;
use Hobosoft\MegaLoader\MegaLoader;
use Hobosoft\MegaLoader\MiniLogger;
use Hobosoft\MegaLoader\Utils;
use Psr\Log\LoggerInterface as PsrLoggerInterface;
use Psr\Log\NullLogger;

class Composer
{
    protected array $composerFiles = [];

    protected array $psr0 = [];
    protected array $psr4 = [];
    protected array $files = [];
    protected array $classMap = [];
    private bool $isLoaded = false;

    public function __construct(
        protected PsrLoggerInterface|MiniLogger $logger,
        protected string                        $path,
        protected int                           $pathMaxDepth = -1,
        protected array                         $pathExclude = [],
        protected array                         $namespaceExclude = [],
    )
    {
        $this->logger ??= new NullLogger();
        $this->composerFiles = [
            Utils::joinPaths(ROOTPATH, 'composer.json') => static fn($fn) => new ComposerJson($fn),
        ];
    }

    private function getComposerJson(string $filename = null, bool $allowLoading = false): ComposerJson
    {
        $filename ??= array_key_first($this->composerFiles);
        $ret = match (true) {
            isset($this->composerFiles[$filename]) => $this->composerFiles[$filename],
            $allowLoading === false => throw new \Exception(__METHOD__ . ":  The file '$filename' doesn't exist in our composer file list (try enabling allowLoading flag)."),
            default => $this->composerFiles[$filename] = new ComposerJson($filename),
        };
        return ($ret instanceof \Closure) ? ($this->composerFiles[$filename] = ($ret)($filename)) : $ret;
    }

    private function find(string $path): array
    {
        $files = (new FileFinder())
            ->wantDirs(false)
            ->wantFiles(true)
            ->maxDepth($this->pathMaxDepth)
            ->excludePath($this->pathExclude)->excludePath(['*/vendor', '*/var', '*/tests', '*/bin'])
            ->includePath($path)
            ->acceptFileCallback(fn($fn) => (basename($fn) === 'composer.json'))
            ->find();
        return $files;
    }

    public function get(string $key, mixed $default = null): mixed
    {
        $keys = explode('.', $key);
        $ret = $this->getComposerJson()->toArray();
        foreach ($keys as $k => $v) {
            unset($keys[$k]);
            if (array_key_exists($v, $ret) === false) {
                return $default;
            }
            $ret = $ret[$v];
        }
        return $ret;
    }

    public function set(string $name, mixed $data): void
    {
        throw new \Exception("set() not implemented.");
    }

    public function loadAutoload(string $basepath): array
    {
        if ($this->isLoaded === false) {
            $vendor = Utils::joinPaths($basepath, 'vendor');
            $dir = Utils::joinPaths($vendor, 'composer');

            if (file_exists($file = $dir . '/autoload_namespaces.php')) {
                $map = require $file;
                foreach ($map as $namespace => $path) {
                    $this->psr0[$namespace] = $path;
                }
            }

            if (file_exists($file = $dir . '/autoload_psr4.php')) {
                $map = require $file;
                foreach ($map as $namespace => $path) {
                    $this->psr4[$namespace] = $path;
                }
            }

            if (file_exists($file = $dir . '/autoload_classmap.php')) {
                $classMap = require $file;
                if ($classMap) {
                    $classMapDiff = array_diff_key($classMap, $this->classMap);
                    $this->classMap += $classMapDiff;
                }
            }

            if (file_exists($file = $dir . '/autoload_files.php')) {
                $includeFiles = require $file;
                foreach ($includeFiles as $includeFile) {
                    $relativeFile = $this->stripVendorDir($includeFile, $vendor);
                    //Utils::includeFile($includeFile);
                    $this->files[$relativeFile] = $includeFile;
                }
            }
        }
        return [
            'psr-0' => $this->psr0,
            'psr-4' => $this->psr4,
            'files' => $this->files,
            'classmap' => $this->classMap,
        ];
    }

    /**
     * Removes the vendor directory from a path.
     * @param string $path
     * @return string
     */
    protected function stripVendorDir($path, $vendorDir)
    {
        $path = realpath($path);
        $vendorDir = realpath($vendorDir);

        if (strpos($path, $vendorDir) === 0) {
            $path = substr($path, strlen($vendorDir) + 1);
        }

        return $path;
    }
}
