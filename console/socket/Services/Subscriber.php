<?php

namespace console\socket\Services;

use Swoole\WebSocket\Server;

/**
 * Class Subscriber
 *
 * @property Server $server
 */
class Subscriber
{
    private Server $server;

    public function __construct(Server $server)
    {
        $this->server = $server;
    }

    public function subscribe(array $list, int $connectionId): void
    {
        foreach ($list as $value) {
            $this->server->channelList[$value][$connectionId] = $connectionId;
            $this->server->redis->subscribe($value);
        }
    }

    public function __destruct()
    {
        unset($this->server);
    }
}
