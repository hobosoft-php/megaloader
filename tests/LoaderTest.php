<?php

namespace Hobosoft\MegaLoader\Tests;

use Hobosoft\MegaLoader\Contracts\LocatorInterface;
use Hobosoft\MegaLoader\MegaLoader;
use Psr\Log\NullLogger;

define('ROOTPATH', dirname(__DIR__));
require __DIR__.'/../vendor/autoload.php';


class TestLookup1 implements LocatorInterface
{
    public function locate(string $className): ?string
    {
        print(__CLASS__ . " looking up!\n");
    }
}

class TestLookup2 implements LocatorInterface
{
    public function locate(string $className): ?string
    {
        print(__CLASS__ . " looking up!\n");
    }
}

it('class creation', function () {
    $loader = new MegaLoader(new NullLogger());
    $loader->addLocator(TestLookup1::class);
    $loader->addLocator(TestLookup2::class);
});
