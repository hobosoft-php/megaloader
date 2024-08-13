<?php

namespace Hobosoft\MegaLoader\Locators;

use Hobosoft\Boot\PathEnum;
use Hobosoft\Boot\Paths;
use Hobosoft\MegaLoader\MegaLoader;

class ClassMapLocator extends AbstractLocator
{
    private string $cacheFile;
    private array $map;

    public function locate(string $className): string|bool
    {
        if(isset($this->cacheFile) === false) {
            $this->cacheFile = Paths::join(PathEnum::CACHE, MegaLoader::CACHE_SECTION, 'classMap.php');
            if(file_exists($this->cacheFile) === false) {
                $this->map = $this->generateClassMap($this->config[MegaLoader::CONFIG_SECTION]);
                file_put_contents($this->cacheFile, var_export($this->map, true));
            }
            else {
                $this->map = include $this->cacheFile;
            }
        }
        die("Class ".__CLASS__." is not functional yet.");
    }

    private function generateClassMap(array $config): array
    {
        $ret = [];
        foreach($config as $path) {
            if(is_file($path) === true) {
                $content = file_get_contents($path);
                $info = $this->parse($content);
                $ns = ($info['namespace'] ?? '') . '\\';
                foreach($info['objects'] as $name) {
                    $ret[$ns . $name] = $path;
                }
            }
        }
        return $ret;
    }

    private function scanPath(string $path): array
    {
        $ret = [];
        $files = scandir($path);
        foreach($files as $file) {
            if($file === '.' || $file === '..') {
                continue;
            }
            if(is_dir(($fn = Paths::join($path, $file)))) {
                $ret += $this->scanPath($path.$file.'/');
            }
            else if(is_file($fn) && pathinfo($file, PATHINFO_EXTENSION) === 'php') {
                $ret += $fn;
            }
        }
        return $ret;
    }

    private function parse(string $str): array
    {
        $classes = $nsPos = $final = array();
        $foundNS = FALSE;
        $ii = 0;
        $er = error_reporting();
        error_reporting(E_ALL ^ E_NOTICE);

        $php_code = $str;
        $tokens = token_get_all($php_code);
        $count = count($tokens);

        for ($i = 0; $i < $count; $i++) {
            if (!$foundNS && $tokens[$i][0] == T_NAMESPACE) {
                $nsPos[$ii]['start'] = $i;
                $foundNS = TRUE;
            } elseif ($foundNS && ($tokens[$i] == ';' || $tokens[$i] == '{')) {
                $nsPos[$ii]['end'] = $i;
                $ii++;
                $foundNS = FALSE;
            } elseif ($i - 2 >= 0 && $tokens[$i - 2][0] == T_CLASS && $tokens[$i - 1][0] == T_WHITESPACE && $tokens[$i][0] == T_STRING) {
                $classes[$tokens[$i][1]] = $i - 4 >= 0 && $tokens[$i - 4][0] == T_ABSTRACT ? 'ABSTRACT CLASS' : 'CLASS';
            } elseif ($i - 2 >= 0 && $tokens[$i - 2][0] == T_INTERFACE && $tokens[$i - 1][0] == T_WHITESPACE && $tokens[$i][0] == T_STRING) {
                $classes[$tokens[$i][1]] = 'INTERFACE';
            } elseif ($i - 2 >= 0 && $tokens[$i - 2][0] == T_TRAIT && $tokens[$i - 1][0] == T_WHITESPACE && $tokens[$i][0] == T_STRING) {
                $classes[$tokens[$i][1]] = 'TRAIT';
            } elseif ($i - 2 >= 0 && $tokens[$i - 2][0] == T_ENUM && $tokens[$i - 1][0] == T_WHITESPACE && $tokens[$i][0] == T_STRING) {
                $classes[$tokens[$i][1]] = 'ENUM';
            }
        }
        error_reporting($er);

        $ns = '';
        foreach ($nsPos as $p) {
            for ($i = $p['start'] + 1; $i < $p['end']; $i++) {
                if (isset($tokens[$i]) && isset($tokens[$i][1])) {
                    $ns .= $tokens[$i][1];
                } else {
                    print_r($tokens[$i]);
                }
            }
            $ns = trim($ns);
        }
        return array('namespace' => $ns, 'objects' => $classes);
    }
}