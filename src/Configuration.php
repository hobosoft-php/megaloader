<?php

namespace Hobosoft\MegaLoader;

use Hobosoft\Boot\PathEnum;
use Hobosoft\Boot\Paths;
use Hobosoft\Config\Schema\Context;
use Hobosoft\Config\Schema\Define;
use Hobosoft\Config\Schema\Exceptions\ValidationException;
use Hobosoft\Config\Schema\Processor;
use Hobosoft\Config\Schema\Types\Structure;
use stdClass;

class Configuration //implements ConfigurationInterface
{
    const string CACHE_FOLDER_NAME = 'classloader-' . PHP_SAPI;
    private static Structure $schema;

    public static function getSchema(): Structure
    {
        if (isset(self::$schema) === false) {
            $transformCacheEnabled = function ($value, Context $context) {
                return match (true) {
                    is_numeric($value) => $value > 0,
                    is_string($value) => $value === 'true',
                    is_bool($value) => $value,
                    default => throw new \Exception("Invalid type"),
                };
            };
            self::$schema = Define::structure([
                'prepend' => Define::bool('true'),
                'replaceComposer' => Define::bool('true'),
                'cache' => Define::structure([
                    'enabled' => Define::anyOf('true', 'false', true, false, 1, 0)->castTo('bool')->default(false)->transform($transformCacheEnabled),
                    'backend' => Define::string('FileCache'),
                    'path' => Define::string(Utils::joinPaths(Utils::getDefinedPath('cache'), self::CACHE_FOLDER_NAME)),
                ])->castTo('array'),
                'psr-0' => Define::arrayOf('string', 'string'),
                'psr-4' => Define::arrayOf('string', 'string'),
                'files' => Define::listOf('string')->description('Source files/directories to be included when autoloader loads.'),
                'classmap' => Define::listOf('string')->description('Source files/directories to be scanned to make the class map.'),
                'exclude-from-classmap' => Define::listOf('string')->description('Source files/directories to be excluded from the class map.'),
                'locators' => Define::structure([
                    'class' => Define::arrayOf('string', 'string'),
                    'plugin' => Define::arrayOf('string', 'string'),
                    'module' => Define::arrayOf('string', 'string'),
                    'config' => Define::arrayOf('string', 'string'),
                ])->castTo('array'),
                'loaders' => Define::structure([
                    'class' => Define::string(),
                    'plugin' => Define::string(),
                    'module' => Define::string(),
                    'config' => Define::string(),
                ])->castTo('array'),
                'decorators' => Define::structure([
                    'class' => Define::arrayOf('string', 'string'),
                    'plugin' => Define::arrayOf('string', 'string'),
                    'module' => Define::arrayOf('string', 'string'),
                    'config' => Define::arrayOf('string', 'string'),
                ])->castTo('array'),
            ])->castTo('array');
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
            echo __CLASS__.': Data is invalid: ' . $e->getMessage();
        }
        return is_array($normalized) ? $normalized : self::stdToArray($normalized);
    }

    private static function stdToArray(stdClass $std): array
    {
        foreach (($ret = (array)$std) as &$item) {
            if (is_object($item)) {
                $item = self::stdToArray($item);
            }
        }
        return $ret;
    }
}

