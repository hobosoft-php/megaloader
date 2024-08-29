<?php

namespace Hobosoft\MegaLoader;

use Hobosoft\Boot\PathEnum;
use Hobosoft\Boot\Paths;
use hoge\fuga\product\Exception;
use function Symfony\Component\Translation\t;

class Utils
{
    protected static array $includedFiles = [];
    protected static array $includedErrors = [];

    protected static bool $registered = false;

    public static function includeFile(string $fn): bool
    {
        if(self::$registered === false) {
            self::$registered = true;
            $fn = Paths::join(ROOTPATH, PathEnum::VAR, 'debug/included_files-megaloader.txt');
            if(is_dir(($dir = dirname($fn))) === false) {
                mkdir($dir, 0777, true);
            }
            register_shutdown_function(function () use($fn) {
                $content = '';
                $n = 0;
                foreach (self::$includedFiles as $file => $status) {
                    $content .= $n++.":  $file\n";
                }
                file_put_contents($fn, $content);
            });
        }
        $formatTrace = function(array $trace): string {
            $ret = '';
            foreach ($trace as $item => $data) {
                $ret .= "$item: ".(is_array($data) ? '<array>' : $data)."\n";
            }
            return $ret;
        };
        if(array_key_exists($fn, self::$includedFiles)) {
            //throw new \Exception("File '$fn' is already included by MegaLoader.");
            self::$includedErrors[$fn] = ['error' => true, 'message' => "File is already included by MegaLoader.\n\n".$formatTrace(debug_backtrace())];
            return false;
        }
        else if(in_array($fn, get_included_files())) {
            //throw new \Exception("File '$fn' is already included by something else.");
            self::$includedErrors[$fn] = ['error' => true, 'message' => "File is already included by something else.\n\n".$formatTrace(debug_backtrace())];
            return false;
        }
        /*else if (str_starts_with($fn, '/') && str_starts_with($fn, ROOTPATH) === false) {
            self::$includedErrors[$fn] = ['error' => true, 'message' => "This method only accepts relative paths or absolute paths beginning with ".ROOTPATH.".\n\n".$formatTrace(debug_backtrace())];
            throw new \Exception("This method only accepts relative paths or absolute paths beginning with ".ROOTPATH.".\n\n".$formatTrace(debug_backtrace()));
        }*/
        else if (file_exists(($fn)) === false) {
            self::$includedErrors[$fn] = ['error' => true, 'message' => "Include file '$fn' not found, please check autoloader configuration.\n\n".$formatTrace(debug_backtrace())];
            throw new \Exception("Include file '$fn' not found, please check autoloader configuration.\n\n".$formatTrace(debug_backtrace()));
        }
        self::$includedFiles[$fn] = ['error' => false, 'message' => 'success'];
        (function(string $fn):void { require $fn; })($fn);
        return true;
    }

    public static function include(string|array $fileList, string $basePath = null): bool
    {
        $fileList = is_array($fileList) ? $fileList : [$fileList];
        while(!empty($fileList)) {
            if(is_array(($fn = array_shift($fileList)
            ))) {
                $fileList = array_merge($fileList, $fn);
                continue;
            }
            if($fn[0] !== '/' && is_null($basePath) === false) {
                $fn = rtrim($basePath, '\\/') . DIRECTORY_SEPARATOR . ltrim($fn, '\\/');
            }
            self::includeFile($fn);
        }
        return true;
    }

    public static function fullPathGlob(string $filter, string $basePath = ''): array
    {
        $ret = [];
        foreach (glob(Paths::join($basePath, $filter)) as $file) {
            $ret[] = Paths::canonicalize($file);
        }
        return $ret;
    }

    public static function getIncludedFiles(): array
    {
        return self::$includedFiles;
    }

    public static function mergeArrays(array $array1, array $array2): array
    {
        return array_merge($array1, $array2);
    }

    public static function validateInstance(mixed $param, string $class, bool $throwException = true): bool
    {
        if(($ret = ($param instanceof $class)) === false && $throwException === true) {
            throw new \Exception(__FUNCTION__ . ":  validation failed!  Class '".get_class($param)."' does not match class type '$class'");
        }
        return $ret;
    }

}
