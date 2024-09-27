<?php

namespace Hobosoft\MegaLoader;

class Utils
{
    protected static array $includedFiles = [];
    protected static array $includedErrors = [];

    protected static mixed $config;
    protected static bool $registered = false;

    public static function joinPaths(string ...$paths): string
    {
        $ret = rtrim(array_shift($paths), '\\/');
        while (!empty($paths)) {
            $ret .= DIRECTORY_SEPARATOR . trim(array_shift($paths), '\\/');
        }
        return $ret;
    }

    public static function formatBacktrace(array $trace): string
    {
        $ret = '';
        foreach ($trace as $item => $data) {
            $ret .= "$item: " . (is_array($data) ? '<array>' : $data) . "\n";
        }
        return $ret;
    }

    public static function wildcardReplace(string $pattern, string $subject): string
    {
        $pattern = strtr($pattern, array(
            '*' => '.*?', // 0 or more (lazy) - asterisk (*)
            '?' => '.', // 1 character - question mark (?)
        ));
        return preg_match("/$pattern/", $subject);
    }

    public static function includeFile(string $fn, int $flags = 0): bool
    {
        $fn = str_replace('\\\\', '\\', $fn);
        if (self::$registered === false) {
            self::$registered = true;
            $logfn = self::joinPaths(ROOTPATH, 'var/debug-'.PHP_SAPI.'/included_files-megaloader.txt');
            if (is_dir(($dir = dirname($logfn))) === false) {
                @mkdir($dir, 0777, true) or die("Failed to create directory $dir.");
            }
            register_shutdown_function(function () use ($logfn) {
                $content = '';
                $n = 0;
                foreach (self::$includedFiles as $file => $status) {
                    $content .= $n++ . ":  $file\n";
                }
                file_put_contents($logfn, $content);
            });
        }
        if (array_key_exists($fn, self::$includedFiles)) {
            self::$includedErrors[$fn] = ['error' => true, 'message' => "File is already included by MegaLoader.\n\n"];
            throw new \Exception("File '$fn' is already included by MegaLoader.");
        } else if (in_array($fn, get_included_files())) {
            self::$includedErrors[$fn] = ['error' => true, 'message' => "File is already included by something else.\n\n"];
            throw new \Exception("File '$fn' is already included by something else.");
        }
        else if (file_exists(($fn)) === false) {
            self::$includedErrors[$fn] = ['error' => true, 'message' => "Include file '$fn' not found, please check autoloader configuration.\n"];
            throw new \Exception("Include file '$fn' not found, please check autoloader configuration.");
        }
        self::$includedFiles[$fn] = ['error' => false, 'message' => 'success'];
        (function (string $fn): void {
            require $fn;
        })($fn);
        return true;
    }

    const int NO_EXCEPTION_ON_FAILURE = 1;
    const int ALLOW_RECURSIVE_GLOB = 2;
    const int ALLOW_GLOB = 4;

    private static function str_contains_any(string $haystack, array $needles): bool
    {
        return array_reduce($needles, fn($a, $n) => $a || str_contains($haystack, $n), false);
    }

    private static function filesOnly(array &$list): array
    {
        foreach($list as $k => $file) {
            if(is_file($file) &&  file_exists($file)) {
                continue;
            }
            unset($list[$k]);
        }
        return $list;
    }

    public static function includeArray(array $fileList, string $basePath = null, int $flags = 0): bool
    {
        $errors = [];
        while(!empty($fileList)) {
            $fileName = array_shift($fileList);
            if(is_array($fileName) === true) {
                $fileList += $fileName;
                continue;
            }
            if(self::isPathAbsolute($fileName) === false) {
                $fileName = self::joinPaths($basePath, $fileName);
            }
            if($flags & self::ALLOW_GLOB && self::str_contains_any($fileName, ['*', '?', '[', ']'])) {
                $r = glob($fileName);
                $fileList = array_merge($fileList, self::filesOnly($r));
            }
            else {
                try {
                    self::includeFile($fileName, $flags);
                }
                catch (\Exception $e) {
                    print("Error including $fileName.\n");
                    $errors[] = $e->getMessage();
                }
            }
        }
        if(count($errors) > 0) {
            throw new \Exception("Error including files:  \n   ".implode("\n   ", $errors));
        }
        return true;
    }

    public static function includePath(string $path): bool
    {
        if(str_starts_with($path, '/') === false) {
            $path = self::joinPaths(ROOTPATH, $path);
        }
        if(is_dir($path = rtrim($path, '\\/')) === false) {
            return false;
        }
        self::includeArray(glob($path . "/*.php"));
        return true;
    }

    public static function fullPathGlob(string $filter, string $basePath = null): array
    {
        if (self::isPathRelative($filter)) {
            $basePath = is_null($basePath) ? ROOTPATH : $basePath;
            $filter = self::joinPaths($basePath, $filter);
        }
        $ret = [];
        foreach (glob($filter) as $file) {
            //$ret[] = Paths::canonicalize($file);
            $ret[] = $file;
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
        if (($ret = ($param instanceof $class)) === false && $throwException === true) {
            throw new \Exception(__FUNCTION__ . ":  validation failed!  Class '" . get_class($param) . "' does not match class type '$class'");
        }
        return $ret;
    }

    public static function isPathAbsolute($path)
    {
        if ('' === $path) {
            return false;
        }

        // Strip scheme
        if (false !== ($pos = strpos($path, '://'))) {
            $path = substr($path, $pos + 3);
        }

        // UNIX root "/" or "\" (Windows style)
        if ('/' === $path[0] || '\\' === $path[0]) {
            return true;
        }

        // Windows root
        if (strlen($path) > 1 && ctype_alpha($path[0]) && ':' === $path[1]) {
            // Special case: "C:"
            if (2 === strlen($path)) {
                return true;
            }

            // Normal case: "C:/ or "C:\"
            if ('/' === $path[2] || '\\' === $path[2]) {
                return true;
            }
        }

        return false;
    }

    public static function isPathRelative(string $param)
    {
        return !(self::isPathAbsolute($param));
    }

    public static function getRootPath(): string
    {
        if(defined('ROOTPATH')) {
            return ROOTPATH;
        }
        $ret = dirname($_SERVER['PHP_SELF']);
        if(file_exists($ret . '/composer.json') === false) {
            $ret = dirname($ret);
            if(file_exists($ret . '/composer.json') === false) {
                throw new \Exception("Not able to determine root path, composer.json not found");
            }
        }
        return $ret;
    }

    private static array $paths;
    public static function getDefinedPaths(): array
    {
        if(isset(self::$paths) === false) {
            $defs = include (($root = Utils::getRootPath()) . '/config/paths.php');
            self::$paths = [];
            $prefix = [];
            while(!empty($defs)) {
                $k = array_key_first($defs);
                $v = array_shift($defs);
                if(is_array($v) === false) {
                    self::$paths[$k] = $k;
                    continue;
                }
                self::$paths[$k] = $k;
                foreach($v as $kk => $vv) {
                    $vv = $k . '/' . $kk;
                    self::$paths[$kk] = $vv;
                }
            }
        }
        return self::$paths;
    }

    public static function getDefinedPath(string $name): string
    {
        $name = strtolower($name);
        $paths = self::getDefinedPaths();
        if(isset($paths[$name]) === true) {
            return $paths[$name];
        }
        throw new \InvalidArgumentException("Unknown path '$name'.");
    }
}
