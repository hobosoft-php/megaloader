<?php

namespace Library\Classloader\Loaders;

use Library\Classloader\Contracts\ClassLoaderInterface;
use Library\Config\Definitions\Exceptions\Exception;
use Psr\Log\LoggerInterface as PsrLoggerInterface;

class LoaderGroup extends AbstractLoader
{
    protected array $loaders = [];

    public function __construct(
        array $loaders,
        ?PsrLoggerInterface $logger,
        ?array $config = null,
        ?ClassLoaderInterface $fallback = null,
    )
    {
        parent::__construct($logger, $config);
        $this->loaders = [];
        foreach ($loaders as $loader) {
            $this->loaders[] = match(true) {
                $loader instanceof ClassLoaderInterface => static fn() => $loader,
                $loader instanceof \Closure => $loader,
                is_string($loader) => static fn() => new ($loader)($logger, $config),
                default => throw new Exception("Loader '{$loader}' not implemented"),
            };
        }
    }

    public function addLoader(mixed $loader): void
    {
        $this->loaders[] = $loader;
    }

    public function getLoader(string|int $nameOrIndex): ClassLoaderInterface|bool
    {
        if(is_string($nameOrIndex) === true) {
            return $this->loaders[$nameOrIndex] ?? false;
        }
        else {
            return array_values($this->loaders)[$nameOrIndex];
        }
        return false;
    }

    public function lookupClass(string $className): ?string
    {
        for($i=0, $iMax = count($this->loaders); $i< $iMax; $i++) {
            if(is_string($this->loaders[$i])) {
                $this->loaders[$i] = new ($this->loaders[$i])();
            }
            else if($this->loaders[$i] instanceof \Closure) {
                $this->loaders[$i] = ($this->loaders[$i])();
            }
            else if($this->loaders[$i] instanceof ClassLoaderInterface) {
                //$this->loaders[$i] = $this->loaders[$i];
            }
            else {
                print('');
            }
            if (($arr = $this->loaders[$i]->lookupClass($className)) !== null) {
                return $arr;
            }
        }
        return ($this->fallback !== null) ? $this->fallback->lookupClass($className) : null;
    }
}