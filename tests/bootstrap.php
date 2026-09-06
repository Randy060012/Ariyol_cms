<?php

/*
|--------------------------------------------------------------------------
| Bootstrap des tests
|--------------------------------------------------------------------------
|
| Sur certaines sessions (notamment Windows), l'environnement shell exporte
| les variables du .env réel. PHPUnit ne peut pas écraser $_SERVER, or le
| lecteur dotenv de Laravel lit $_SERVER en priorité. On neutralise donc
| ces variables ici, AVANT le démarrage de l'application, pour que les
| valeurs déclarées dans phpunit.xml (base SQLite en mémoire, cache array,
| session array, APP_ENV=testing) soient bien appliquées.
|
*/

foreach ($_SERVER as $key => $value) {
    if (is_string($key) && preg_match('/^(APP_|SESSION_|CACHE_|DB_|MAIL_|QUEUE_|BROADCAST_|FILESYSTEM_|AWS_|PULSE_|TELESCOPE_|NIGHTWATCH_)/', $key)) {
        unset($_SERVER[$key]);
    }
}

require __DIR__.'/../vendor/autoload.php';
