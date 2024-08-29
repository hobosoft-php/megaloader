<?php

namespace Hobosoft\MegaLoader\Locators;

use Hobosoft\MegaLoader\Composer\Composer;
use Hobosoft\MegaLoader\Contracts\LocatorInterface;
use Hobosoft\MegaLoader\Locators\AbstractLocator;
use Hobosoft\MegaLoader\MegaLoader;

class ComposerLocator extends LocatorDelegator
{
    private Composer $composer;
    private array $autoload = [];
    private array $classMap = [];
    private array $missingMap = [];
    private LocatorInterface $locator;

    private static array $keys = [ 'psr-0', 'psr-4', 'files', 'classmap', 'exclude-from-classmap' ];

    public function __construct(
        MegaLoader $loader,
    )
    {
        parent::__construct($loader, [
            static fn($th) => new MapLocator($th, 'composer.autoload'),
            static fn($th) => new Psr4Locator($th, 'composer.autoload'),
            static fn($th) => new Psr0Locator($th, 'composer.autoload'),
        ]);
        $this->composer = new Composer($this->loader->getLogger(), ROOTPATH);
        $this->autoload = $this->composer->loadAutoload(ROOTPATH);
        $loader->getConfig()->set('composer', [
            'autoload' => $this->autoload,
        ]);
        /*foreach(self::$keys as $key) {
            $o1 = $cfg->get(MegaLoader::CONFIG_SECTION . '.' . $key) ?? [];
            $o2 = $this->autoload[$key] ?? [];
            $cfg->set(MegaLoader::CONFIG_SECTION . '.' . $key, array_merge($o1, $o2));
        }*/
    }

    public function locate(string $name): string|bool
    {
        $ret = parent::locate($name);
        return $ret;
    }

}
