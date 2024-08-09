<?php

namespace Hobosoft\MegaLoader;

use Library\Boot\Paths;
use Library\Config\Definitions\Builder\TreeBuilder;
use Library\Config\Definitions\Exceptions\Exception;
use Library\Config2\Contracts\ConfigurationInterface;
use Library\Config2\Schema\Context;
use Library\Config2\Schema\Define;
use Library\Config2\Schema\Exceptions\ValidationException;
use Library\Config2\Schema\Processor;
use Library\Config2\Schema\Types\Structure;

class Configuration //implements ConfigurationInterface
{
    const string CACHE_FOLDER_NAME = 'classloader-' . PHP_SAPI;
    private static Structure $schema;

    public static function getConfigDefault(): array|\stdClass
    {
        $transformCacheEnabled = function ($value, Context $context) {
            switch(true) {
                case is_bool($value): return $value;
                case is_string($value): return ($value === 'true') ? true : false;
                case is_numeric($value): return ($value <= 0) ? false : true;
                default: throw new Exception("Invalid type");
            }
        };

        //$config->load(Paths::get(\Path::CONFIG));

        if(isset(self::$schema) === false) {
            self::$schema = Define::structure([
                'cache' => Define::structure([
                    'enabled' => Define::anyOf('true', 'false', true, false, 1, 0)->castTo('bool')->transform($transformCacheEnabled),
                    'backend' => Define::string('FileCache'),
                    'path' => Define::string(Paths::join(Path::CACHE, self::CACHE_FOLDER_NAME)),
                ])->castTo('array'),
                'maxDepth' => Define::int(10)->min(0),
                'includes' => Define::list([
                    'src',
                    'library',
                    'plugins',
                    'vendor',
                ]),
                'excludes' => Define::list([
                    'templates',
                    'var',
                ]),
                'psr-4' => Define::structure([
                    'Source\\' => Define::list(['src/', 'source/']),
                    'Library\\' => Define::list(['lib/', 'library/']),
                    'Modules\\' => Define::list(['modules/']),
                    'Plugins\\' => Define::list(['plugins/']),
                    'Application\\' => Define::list(['app/', 'src/app/']),
                    'Psr\\Container\\' => Define::list(['vendor/psr/container/src/']),
                ])->castTo('array'),
            ]);
        }

        try {
            $normalized = (new Processor())->process(self::$schema, []);
        } catch (ValidationException $e) {
            echo 'Data is invalid: ' . $e->getMessage();
        }
        return $normalized;
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