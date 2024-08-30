<?php

use Hobosoft\MegaLoader\MiniLoader;

require_once __DIR__ . '/src/MiniLoader.php';

return new MiniLoader(null, include __DIR__.'/config/config.php');

/*
if (class_exists(\Hobosoft\MegaLoader\MiniLoader::class, false) === false) {

    $map = [
//        'Hobosoft\\Boot\\' => ROOTPATH . '/vendor/hobosoft/boot/src/',
//        'Hobosoft\\Config\\' => ROOTPATH . '/vendor/hobosoft/config/src/',
        'Hobosoft\\MegaLoader\\' => __DIR__ . '/src/',
    ];

    include __DIR__ . '/../vendor/autoload.php';

    $whoops = new \Whoops\Run;
    $whoops->pushHandler(new \Whoops\Handler\PlainTextHandler());
    $whoops->register();

    var_export(get_included_files());

    include __DIR__ . '/Utils.php';

    var_export(get_included_files());

    include __DIR__ . '/MiniLoader.php';

    var_export(get_included_files());

    $loader = new \Hobosoft\MegaLoader\MiniLoader($map);


    //include __DIR__ . '/Utils.php';
    //include __DIR__ . '/MegaLoader.php';

    //class_alias(\Hobosoft\MegaLoader\Utils::class, 'LoaderUtils');

    //$loader = new \Hobosoft\MegaLoader\MegaLoader(new \Psr\Log\NullLogger(), [], null);

}
*/