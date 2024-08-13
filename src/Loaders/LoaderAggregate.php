<?php

namespace Hobosoft\MegaLoader\Loaders;

use Hobosoft\Config\Contracts\ConfigInterface;
use Hobosoft\MegaLoader\Contracts\LoaderInterface;
use Psr\Log\LoggerInterface as PsrLoggerInterface;

class LoaderAggregate extends AbstractLoader
{
    protected array $loaders = [];

    public function __construct(
        protected PsrLoggerInterface $logger,
        protected ConfigInterface    $config,
        array                        $loaders = [],
    )
    {
        parent::__construct($logger, $config);
        foreach ($loaders as $loader) {
            $this->addLoader($loader, false);
        }
    }

    public function addLoader(\Closure|LoaderInterface|string $loader, bool $prepend = true): void
    {
        $loader = match(true) {
            $loader instanceof LoaderInterface || $loader instanceof \Closure => $loader,
            is_string($loader) => static fn() => new $loader($this->logger, $this->config),
            default => throw new \Exception("Loader class type '".get_class($loader)."' not implemented"),
        };
        if($prepend) {
            $this->loaders = [$loader] + $this->loaders;
        }
        else {
            $this->loaders = $this->loaders + [$loader];
        }
    }

    public function load(string $className): bool
    {
        for($i=0, $iMax = count($this->loaders); $i< $iMax; $i++) {
            if($this->loaders[$i] instanceof \Closure) {
                $this->loaders[$i] = ($this->loaders[$i])();
            }
            if (($arr = $this->loaders[$i]->lookupClass($className)) !== null) {
                return $arr;
            }
        }
        return false;
    }
}
