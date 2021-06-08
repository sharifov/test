<?php

namespace sales\model\client\listeners;

use common\components\jobs\CheckClientExcludeIpJob;
use sales\model\client\entity\events\ClientCreatedEvent;
use sales\model\client\entity\events\ClientEventInterface;

class ClientCreatedCheckExcludeListener
{
    public function handle(ClientEventInterface $event): void
    {
        $client = $event->getClient();
        if (!$client->cl_ip) {
            return;
        }

        try {
            $delayJob = 5;
            $job = new CheckClientExcludeIpJob($client->id, $client->cl_ip);
            $job->delayJob = $delayJob;
            \Yii::$app->queue_job->delay($delayJob)->push($job);
        } catch (\Throwable $e) {
            \Yii::error([
                'message' => 'Client Id: ' . $client->id,
                'error' => $e->getMessage()
            ], 'ClientCreatedCheckExcludeListener');
        }
    }
}
