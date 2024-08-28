<?php

namespace Hobosoft\MegaLoader;

use Hobosoft\Boot\PathEnum;
use Hobosoft\Boot\Paths;
use Hobosoft\Config\Schema\Context;
use Hobosoft\Config\Schema\Define;
use Hobosoft\Config\Schema\Exceptions\ValidationException;
use Hobosoft\Config\Schema\Processor;
use Hobosoft\Config\Schema\Types\Structure;
use Hobosoft\MegaLoader\Locators\MapLocator;
use Hobosoft\MegaLoader\Locators\Psr0Locator;
use Hobosoft\MegaLoader\Locators\Psr4Locator;
use stdClass;

class Configuration //implements ConfigurationInterface
{
    const string CACHE_FOLDER_NAME = 'classloader-' . PHP_SAPI;
    private static Structure $schema;

    public static function getSchema(): Structure
    {
        if(isset(self::$schema) === false) {
            $transformCacheEnabled = function ($value, Context $context) {
                return match (true) {
                    is_numeric($value) => $value > 0,
                    is_string($value) => $value === 'true',
                    is_bool($value) => $value,
                    default => throw new \Exception("Invalid type"),
                };
            };
            self::$schema = Define::structure([
                'cache' => Define::structure([
                    'enabled' => Define::anyOf('true', 'false', true, false, 1, 0)->castTo('bool')->default(false)->transform($transformCacheEnabled),
                    'backend' => Define::string('FileCache'),
                    'path' => Define::string(Paths::join(PathEnum::CACHE, self::CACHE_FOLDER_NAME)),
                ])->castTo('array'),
                'psr-0' => Define::arrayOf('string', 'string'),
                'psr-4' => Define::arrayOf('string', 'string'),
                'classMap' => Define::listOf('string')->description('Source files/directories to be scanned to make the class map.'),
                'plugins' => Define::arrayOf('string', 'string')->description('Array of plugin namespace prefix => plugin path'),
                'modules' => Define::arrayOf('string', 'string')->description('Array of module namespace prefix => module path'),
            ]);
        }
        return self::$schema;
    }

    public static function getDefault(): array
    {
        return self::process([]);
    }

    public static function process(array $config): array
    {
        $normalized = [];
        try {
            $normalized = (new Processor())->process(self::getSchema(), $config);
        } catch (ValidationException $e) {
            echo 'Data is invalid: ' . $e->getMessage();
        }
        return self::stdToArray($normalized);
    }

    private static function stdToArray(stdClass $std): array
    {
        foreach(($ret = (array)$std) as &$item) {
            if(is_object($item)) {
                $item = self::stdToArray($item);
            }
        }
        return $ret;
    }
}



    /*public function getConfigTreeBuilder(): TreeBuilder
    {
        ($treeBuilder = new TreeBuilder('classloader'))->getRootNode()
            ->children()
                ->scalarNode('root_path')->end()
                ->scalarNode('max_depth')->end()
                ->scalarNode('cache_path')->end()
                ->booleanNode('cache_enabled')->end()
                ->arrayNode('binary_includes')
                    //->scalarPrototype()->end()
                ->end()
                ->arrayNode('includes')
                    //->scalarPrototype()->end()
                ->end()
                ->arrayNode('excludes')
                    //->scalarPrototype()->end()
                ->end()
                ->arrayNode('map')
                    // ->scalarPrototype()->end()
                ->end()
            ;

        return $treeBuilder;
   }

    'autoloader' => [
        'rootPath' => ROOTPATH,
        'maxDepth' => 10,
        'cachePath' => ROOTPATH . '/var/cache/autoloader',
        'cacheEnabled' => true,
        'scannerClass' => 'Library\\Autoloader\\Scanners\\PhpScanner',
        'loaderClass' => 'Library\\Autoloader\\Loaders\\ClassMapLoader',
        'addDefaultLoader' => true,
        'excludePaths' => array(
            ROOTPATH . '/assets',
            ROOTPATH . '/bin',
            ROOTPATH . '/config',
            ROOTPATH . '/migrations',
            ROOTPATH . '/public',
            ROOTPATH . '/templates',
            ROOTPATH . '/tests',
            ROOTPATH . '/translations',
            ROOTPATH . '/var',
            ROOTPATH . '/vendor',
        ),
        'includePaths' => array(
            ROOTPATH . '/app',
            ROOTPATH . '/lib',
            ROOTPATH . '/modules',
            ROOTPATH . '/modules-test',
            ROOTPATH . '/plugins',
        ),
        'replaceStrings' => array(
            ['Symfony\\Bundle\\', 'bundles/framework-bundle/'],
            ['Application', 'app'],
            ['Plugins', 'plugins'],
            ['Library', 'lib'],
            ['Source', 'src'],
        ),
 */