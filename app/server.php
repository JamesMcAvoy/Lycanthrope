<?php

use Ratchet\Server\IoServer;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;
use Lycanthrope\Main as LycanthropeGame;
use Lycanthrope\Config;
use Lycanthrope\Exception\ExceptionInterface as IException;

require __DIR__ .'/../vendor/autoload.php';

try {
    Config::boot();

    $server = IoServer::factory(
        new HttpServer(
            new WsServer(
                new LycanthropeGame()
            )
        ),
        8080
    );

    $server->run();
    // La nuit tombe...
} catch(IException $e) {
    die("{$e->getMessage()}\n");
}
