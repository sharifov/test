<?php

namespace webapi\src\client;

/**
 * Class RequestBo
 *
 * @property \yii\httpclient\Client $next
 */
class RequestBo
{
    public $next;

    private $authorization;

    public function __construct(\yii\httpclient\Client $client, $authorization)
    {
        $this->next = $client;
    }
}
