<?php
/**
* References: 
*
* 	http://symfony.com/doc/current/cookbook/testing/bootstrap.html
*	https://phpunit.de/getting-started.html
*/
if (isset($_ENV['BOOTSTRAP_DATABASE_REBUILD_ENV'])) {
    passthru(sprintf(
        './database-rebuild --env=test',
        __DIR__,
        $_ENV['BOOTSTRAP_DATABASE_REBUILD_ENV']
    ));
}

require __DIR__.'/bootstrap.php.cache';