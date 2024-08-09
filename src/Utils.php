<?php

namespace Hobosoft\MegaLoader;

class Utils
{
    protected static array $includedFiles = [];

    public static function include(string|array $fileList): bool
    {
        $formatTrace = function(array $trace): string {
            $ret = '';
            foreach ($trace as $item => $data) {
                $ret .= "$item: $data\n";
            }
            return $ret;
        };
        $arr = [];
        foreach((is_array($fileList) ? $fileList : [$fileList]) as $file) {
            $glob = glob($file);
            $arr = array_merge($arr, $glob);
        }
        foreach ($arr as $fn) {
            if (str_starts_with($fn, '/') && str_starts_with($fn, ROOTPATH) === false) {
                self::$includedFiles[$fn] = ['error' => true, 'message' => "This method only accepts relative paths or absolute paths beginning with ".ROOTPATH.".\n\n".$formatTrace(debug_backtrace())];
                throw new \Exception("This method only accepts relative paths or absolute paths beginning with ".ROOTPATH.".\n\n".$formatTrace(debug_backtrace()));
            }
            elseif (file_exists(($fn = ROOTPATH . '/' . $fn)) === false) {
                self::$includedFiles[$fn] = ['error' => true, 'message' => "Include file '$fn' not found, please check autoloader configuration.\n\n".$formatTrace(debug_backtrace())];
                throw new \Exception("Include file '$fn' not found, please check autoloader configuration.\n\n".$formatTrace(debug_backtrace()));
            }
            else {
                self::$includedFiles[$fn] = ['error' => false, 'message' => 'success'];
                (function(string $fn):void { require $fn; })($fn);
            }
        }
        return true;
    }

    public static function getIncludedFiles(): array
    {
        return self::$includedFiles;
    }

    public static function mergeArrays(array $array1, array $array2): array
    {
        return array_merge($array1, $array2);
        //$ret = [];
        //return $ret;
    }

}
