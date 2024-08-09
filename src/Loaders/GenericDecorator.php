<?php

namespace Hobosoft\MegaLoader\Loaders;

use Hobosoft\MegaLoader\Contracts\ClassLoaderInterface;
use Hobosoft\MegaLoader\Contracts\ClassLookupInterface;
use Hobosoft\MegaLoader\Contracts\ManagerInterface;
use Psr\Log\LoggerInterface as PsrLoggerInterface;

class GenericDecorator extends AbstractLoader implements ClassLoaderInterface, ClassLookupInterface
{

    public function __construct(
        ManagerInterface       $parent,
        ?PsrLoggerInterface    $logger,
        protected mixed        $decorated,
        ?array                 $config = null,
    ) {
        parent::__construct($parent, $logger, $config);
    }

    public function getDecorated(): ?ClassLoaderInterface
    {
        return $this->decorated;
    }

    public function lookupClass(string $className): ?string
    {
        return $this->loader->lookupClass($className);
    }
}
