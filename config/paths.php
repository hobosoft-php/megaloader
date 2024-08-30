<?php

/**
 * The array has the defined name as the key, and for
 * the value it is either a boolean for if it is required
 * or not, or possibly an array to define sub-paths.
 *
 * Need to make this more pretty.
 */

return [
    'app' => [
        'Commands' => false,
        'Controllers' => false,
    ],
    'assets' => [
        'css' => false,
        'js' => false,
    ],
    'bin' => true,
    'config' => true,
    'library' => false,
    'modules' => false,
    'plugins' => false,
    'public' => false,
    'src' => true,
    'templates' => false,
    'tests' => false,
    'var' => [
        'build' => false,
        'cache' => true,
        'database' => false,
        'debug' => false,
        'download' => false,
        'log' => true,
        'tmp' => true,
        'user' => false,
        'upload' => false,
    ],
];