<?php

namespace src\listeners\lead;

use modules\objectSegment\src\contracts\ObjectSegmentKeyContract;
use modules\objectSegment\src\object\dto\ClientSegmentObjectDto;
use src\events\lead\LeadSoldEvent;
use src\helpers\app\AppHelper;
use Yii;

class LeadSoldClientReturnIndicationListener
{
    public function handle(LeadSoldEvent $event): void
    {
        try {
            $client = $event->lead->client;
            $dto = new ClientSegmentObjectDto($client);
            Yii::$app->objectSegment->segment($dto, ObjectSegmentKeyContract::TYPE_KEY_CLIENT);
        } catch (\Throwable $e) {
            Yii::error(AppHelper::throwableLog($e, true), 'Listeners:LeadSoldEventLogListener::objectSegment');
        }
    }
}
