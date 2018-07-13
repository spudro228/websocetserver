<?php

declare(strict_types=1);

namespace App\Tests;

use App\ConnectionPool;
use PHPUnit\Framework\TestCase;

class ConnectionPoolTest extends TestCase
{

//    public function testAdd()
//    {
//
//    }

    public function testParseHeaders()
    {
        $data = "
Data: GET / HTTP/1.1\r\n
Host: 127.0.0.1:5556\r\n
Connection: Upgrade\r\n
Pragma: no-cache\r\n
Cache-Control: no-cache\r\n
Upgrade: websocket\r\n
Origin: http://localhost:63342\r\n
Sec-WebSocket-Version: 13\r\n
User-Agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_13_5) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/67.0.3396.99 Safari/537.36\r\n
Accept-Encoding: gzip, deflate, br\r\n
Accept-Language: ru-RU,ru;q=0.9,en-US;q=0.8,en;q=0.7\r\n
Sec-WebSocket-Key: 7JBunxP7UgoQr8cyxLEo1g==\r\n
Sec-WebSocket-Extensions: permessage-deflate; client_max_window_bits\r\n
";

        $c = new ConnectionPool();

        $this->assertArrayHasKey('Sec-WebSocket-Key', $c->parseHeaders($data));
    }
}
