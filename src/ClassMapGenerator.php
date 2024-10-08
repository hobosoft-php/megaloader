<?php

namespace Hobosoft\MegaLoader;

use Hobosoft\Finders\ClassFinder;

class ClassMapGenerator
{
    private CLassFinder $classFinder;
    private array $found;

    public function __construct(
        array $includes = [],
        array $excludes = [],
    )
    {
        $this->classFinder = new ClassFinder($includes, $excludes);
    }

    public function find(string $outputFile): self
    {
        $this->found = $this->classFinder->find();
        return $this;
    }

    public function writeMap(string $filename, string $rootPath = null): self
    {
        $map = [];
        $head =
            "<?php\n"
            ."// autoload_psr4.php @generated by Composer\n"
            ."\n"
            .'$rootPath = "'.($rootPath ?? ROOTPATH).'";'."\n"
            ."\n"
            ."return [\n"
        ;
        $foot =
            "];\n"
            ."\n"
        ;
        foreach ($this->found as $file => $obj) {
            $map[$file] = $obj;
        }
        $str = $head . implode("\n", $map) . $foot;;
        file_put_contents($filename, $str);
        return $this;
    }
}
