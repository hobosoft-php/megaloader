<?php

namespace Hobosoft\MegaLoader\Loaders;

use Hobosoft\MegaLoader\Contracts\ClassLoaderInterface;
use Hobosoft\MegaLoader\Contracts\ManagerInterface;
use Psr\Log\LoggerInterface as PsrLoggerInterface;

class LoaderAggregate extends AbstractLoader
{
    protected array $loaders = [];

    public function __construct(
        ManagerInterface   $parent,
        PsrLoggerInterface $logger,
        array              $loaders,
    )
    {
        parent::__construct($parent, $logger);
        $this->loaders = [];
        foreach ($loaders as $loader) {
            $this->loaders[] = match(true) {
                $loader instanceof ClassLoaderInterface => static fn() => $loader,
                $loader instanceof \Closure => $loader,
                is_string($loader) => static fn() => new ($loader)($parent, $logger),
                default => throw new \Exception("Loader '{$loader}' not implemented"),
            };
        }
    }

    protected function getLoader(string $name): ClassLoaderInterface|null
    {
        return ($this->loaders[$name] ?? null);
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