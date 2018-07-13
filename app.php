<?php

declare(strict_types=1);

require_once './vendor/autoload.php';

use React\EventLoop\Factory;
use React\Socket\ConnectionInterface;
use React\Socket\Server;
use App\ConnectionPool;
$loop = Factory::create();
$server = new Server('127.0.0.1:5556', $loop);
$connectionPool = new ConnectionPool();

$server->on('connection', function (ConnectionInterface $connection) use (&$connectionPool) {
    $connectionPool->add($connection);
    //todo: нужно декодировать вебсокет сообщения
    //toto: выделить хендшейк отдельно
});

$loop->run();