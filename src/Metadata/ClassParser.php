<?php

namespace Hobosoft\MegaLoader\Metadata;

use Psr\Log\LoggerInterface;
use Psr\SimpleCache\CacheInterface;

class ClassParser
{
    public function __construct(
        protected ?LoggerInterface $logger,
        protected ?CacheInterface $cache,
    )
    {

    }

    public function getInfo(string $filename): array
    {
        $ret = $this->parse(file_get_contents($filename));
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