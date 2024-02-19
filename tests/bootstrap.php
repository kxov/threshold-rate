<?php

use Symfony\Component\Dotenv\Dotenv;

require dirname(__DIR__).'/vendor/autoload.php';

if (file_exists(dirname(__DIR__).'/config/bootstrap.php')) {
    require dirname(__DIR__).'/config/bootstrap.php';
} elseif (method_exists(Dotenv::class, 'bootEnv')) {
    (new Dotenv())->bootEnv(dirname(__DIR__).'/.env');
}

if (empty($_SERVER['JETBRAINS_REMOTE_RUN'])) {
    passthru('php bin/console cache:pool:clear cache.global_clearer --env=test');
    passthru('php bin/console doctrine:database:drop --force --env=test');
    passthru('php bin/console doctrine:database:create --env=test --if-not-exists');
    passthru('php bin/console doctrine:migrations:migrate --no-interaction --env=test');
    passthru('php bin/console doctrine:fixtures:load --no-interaction --env=test');
}
