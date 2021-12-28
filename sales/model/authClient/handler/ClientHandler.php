<?php

namespace sales\model\authClient\handler;

use yii\authclient\AuthAction;
use yii\authclient\ClientInterface;

interface ClientHandler
{
    public function handle(AuthAction $authAction, ClientInterface $client): void;

    public function getRedirectUrl(): string;
}
