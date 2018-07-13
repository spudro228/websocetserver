<?php

declare(strict_types=1);


namespace App\Tests\Helpers;


use App\ConnectionPool;
use PHPUnit\Framework\TestCase;
use React\EventLoop\Factory;
use React\EventLoop\LoopInterface;
use React\Promise\Promise;
use React\Socket\Connector;
use React\Socket\ConnectionInterface;
use React\Socket\Server;

class TestKernel extends TestCase
{
    public function createServer($uri = '127.0.0.1:5555'): LoopInterface
    {
        $loop = Factory::create();
        $server = new Server($uri, $loop);

        $connectionPool = new ConnectionPool();

        $server->on('connection', function (ConnectionInterface $connection) use (&$connectionPool) {
            $connectionPool->add($connection);
        });

        $loop->run();

        return $loop;
    }

    public function createClient($uri = '127.0.0.1:5555'): array
    {
        return [$loop = Factory::create(), (new Connector($loop))->connect($uri)];
    }
}