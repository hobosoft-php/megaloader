<?php

test('example', function () {
    $c = new \Hobosoft\MegaLoader\Composer\Composer(
        new \Psr\Log\NullLogger(),
        dirname(__FILE__, 3),
        10,
        [],
        [],
    );
    $c = null;
    expect(true)->toBeTrue();
});
