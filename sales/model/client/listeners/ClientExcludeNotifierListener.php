<?php

namespace sales\model\client\listeners;

use common\models\Lead;
use common\models\Notifications;
use sales\model\client\entity\events\ClientExcludedEvent;

class ClientExcludeNotifierListener
{
    public function handle(ClientExcludedEvent $event): void
    {
        $leads = Lead::find()
            ->byClient($event->clientId)
            ->andWhere(['IS NOT', 'employee_id', null])
            ->with(['client'])
            ->all();
        if (!$leads) {
            return;
        }
        foreach ($leads as $lead) {
            Notifications::createAndPublish(
                $lead->employee_id,
                'Airline test call',
                'Client (' . $lead->client_id . ') ' . $lead->client->getShortName() . ' is detected as Airline test call. Some offers SHOULD be excluded. Contact your manager for advice.',
                Notifications::TYPE_WARNING,
                true
            );
        }
    }
}
