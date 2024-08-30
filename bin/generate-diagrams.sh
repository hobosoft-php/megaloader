#!/bin/bash


#./vendor/bin/php-class-diagram --include='*.php'  src/Loaders/ | plantuml -charset utf-8 -pipe -tpng > var/diagrams/Loaders.png
#./vendor/bin/php-class-diagram --include='*.php'  src/Locators/ | plantuml -charset utf-8 -pipe -tpng > var/diagrams/Locators.png
#./vendor/bin/php-class-diagram --include='*.php'  src/Composer/ | plantuml -charset utf-8 -pipe -tpng > var/diagrams/Composer.png
#./vendor/bin/php-class-diagram --include='*.php'  src/ | plantuml -charset utf-8 -pipe -tpng > var/diagrams/Full.png


SCRIPT_DIR=$1

$SCRIPT_DIR/vendor/bin/php-class-diagram \
        --enable-class-properties \
        | plantuml -charset utf-8 -pipe -tpng > $SCRIPT_DIR/dogfood.png
$SCRIPT_DIR/vendor/bin/php-class-diagram \
        --disable-class-properties \
        | plantuml -charset utf-8 -pipe -tpng > $SCRIPT_DIR/dogfood-model.png
$SCRIPT_DIR/vendor/bin/php-class-diagram \
        --package-diagram $SCRIPT_DIR/src \
        | plantuml -charset utf-8 -pipe -tpng > $SCRIPT_DIR/dogfood-package.png
$SCRIPT_DIR/vendor/bin/php-class-diagram \
        --division-diagram $SCRIPT_DIR/src  \
        | plantuml -charset utf-8 -pipe -tpng > $SCRIPT_DIR/output-division.png

