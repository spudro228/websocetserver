<?php

declare(strict_types=1);

namespace App;

use React\Socket\ConnectionInterface;

class ConnectionPool
{

    /**
     * @var \SplObjectStorage
     */
    protected $connectionPool;

    public function __construct()
    {
        $this->connectionPool = new \SplObjectStorage();
    }

    /**
     * @param ConnectionInterface $connection
     */
    public function add(ConnectionInterface $connection): void
    {
//        $connection->write("Hi!!!\n");

        print "User {$connection->getRemoteAddress()} conected!!! \n";
        $this->connectionPool->attach($connection);
        $this->initEvents($connection);
    }

    /**
     * @param ConnectionInterface $emittedConnection
     */
    protected function initEvents(ConnectionInterface $emittedConnection): void
    {

        $emittedConnection->on('data', function ($chunk) use (&$emittedConnection) {
            //handshake
            \var_dump($chunk);
            $headers = $this->parseHeaders($chunk);
            if (\array_key_exists('Sec-WebSocket-Key', $headers) && $key = $headers['Sec-WebSocket-Key']) {
//                \var_dump($key);
                $key = base64_encode(pack('H*', sha1($key . '258EAFA5-E914-47DA-95CA-C5AB0DC85B11')));
                $headers = "HTTP/1.1 101 Switching Protocols\r\n";
                $headers .= "Upgrade: websocket\r\n";
                $headers .= "Connection: Upgrade\r\n";
                $headers .= "Sec-WebSocket-Version: 13\r\n";
                $headers .= "Sec-WebSocket-Accept:$key\r\n\r\n";
//                \var_dump($headers);

                $emittedConnection->write($headers);
                return;
            }

            $this->sendAll($chunk, $emittedConnection);
        });

        $emittedConnection->on('close', function () use (&$emittedConnection) {
            print 'User: ' . $emittedConnection->getRemoteAddress() . ' disconected...' . PHP_EOL;
            $this->connectionPool->detach($emittedConnection);
            $this->sendAll('User: ' . $emittedConnection->getRemoteAddress() . ' disconected...', $emittedConnection);

        });
    }

    protected function sendAll(string $data, $emittedConnection): void
    {
        /** @var ConnectionInterface $connection */
        foreach ($this->connectionPool as $connection) {
            if ($connection !== $emittedConnection) {
                $connection->write($data . "\n");
            }
        }
    }

    protected function handshakeResponse($request): void
    {

    }

    /**
     * @param string $responseData
     * @return array
     */
    public function parseHeaders(string $responseData): array
    {
        $p = \array_map(function ($x) {
            return array_map('trim', explode(":", $x, 2));
        }, array_filter(array_map('trim', explode("\n", $responseData))));
        return \array_reduce($p, function ($curr, $next) {
            @$curr[$next[0]] = $next[1];
            return $curr;
        }, []);
    }
}