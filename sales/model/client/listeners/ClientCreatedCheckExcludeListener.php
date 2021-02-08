<?php

namespace sales\model\client\listeners;

use common\components\jobs\CheckClientExcludeIpJob;
use sales\model\client\entity\events\ClientChangeIpEvent;

class ClientCreatedCheckExcludeListener
{
    public function handle(ClientChangeIpEvent $event): void
    {
        if (!$event->client->cl_ip) {
            return;
        }

        try {
            $job = new CheckClientExcludeIpJob($event->client->id, $event->client->cl_ip);
            \Yii::$app->queue_job->delay(5)->push($job);
        } catch (\Throwable $e) {
            \Yii::error([
                'message' => 'Client Id: ' . $event->client->id,
                'error' => $e->getMessage()
            ], 'ClientCreatedCheckExcludeListener');
        }
    }
}
