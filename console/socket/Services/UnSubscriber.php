<?php

namespace console\socket\Services;

use Swoole\WebSocket\Server;

/**
 * Class UnSubscriber
 *
 * @property Server $server
 */
class UnSubscriber
{
    private Server $server;

    public function __construct(Server $server)
    {
        $this->server = $server;
    }

    public function unSubscribe(array $list, int $connectionId): void
    {
        foreach ($list as $value) {
            if (isset($this->server->channelList[$value][$connectionId])) {
                unset($this->server->channelList[$value][$connectionId]);

                if (isset($this->server->channelList[$value]) && empty($this->server->channelList[$value])) {
                    unset($this->server->channelList[$value]);
                    $this->server->redis->unsubscribe($value);
                }
            }
        }
    }

    public function __destruct()
    {
        unset($this->server);
    }
}
