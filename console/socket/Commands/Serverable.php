<?php

namespace console\socket\Commands;

use Swoole\WebSocket\Server;

interface Serverable
{
    public function setServer(Server $server);
}
