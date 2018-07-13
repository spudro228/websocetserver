<?php

declare(strict_types=1);


namespace App\Tests;



//use React\Tests\Socket\TestCase;

use App\ConnectionPool;
use PHPUnit\Framework\TestCase;
use Clue\React\Block;
use React\EventLoop\Factory;
use React\Socket\ConnectionInterface;
use React\Socket\Server;


class UpAndDownServerTest extends TestCase
{
    /**
     * @var LoopInterface
     */
    protected $serverLoop;

    /**
     * @var Promise
     */
    protected $client;

    /**
     * @var LoopInterface
     */
    protected $loop;

    public function testExample(): void
    {
        $this->assertSame(1, 1);
    }

//    use MockeryPHPUnitIntegration;

    public function testConnection(): void
    {
        $loop = Factory::create();
        $server = new Server('127.0.0.1:5556', $loop);
//        $server = new ConnectionPool();
        $connectionPool = new ConnectionPool();


        $server->on('connection', function (ConnectionInterface $connection) use (&$connectionPool) {
            $connectionPool->add($connection);
            $this->assertTrue(true);
        });
        $client = stream_socket_client('tcp://localhost:'.'5556');
        fwrite($client, "foo\n");
        $this->assertTrue(true);

        $this->tick($loop);

    }
    private function tick($loop)
    {
        Block\sleep(0, $loop);
    }

}