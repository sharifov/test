<?php

namespace sales\listeners\lead;

use common\components\BackOffice;
use sales\events\lead\LeadQuoteCloneEvent;
use Yii;

/**
 * Class LeadQuoteCloneEventListener
 */
class LeadQuoteCloneEventListener
{

    /**
     * @param LeadQuoteCloneEvent $event
     */
    public function handle(LeadQuoteCloneEvent $event): void
    {
        $data = $event->quote->getQuoteInformationForExpert(true);
        $result = BackOffice::sendRequest('lead/update-quote', 'POST', json_encode($data));
        if ($result['status'] != 'Success' || !empty($result['errors'])) {
            Yii::$app->getSession()->addFlash(
                'warning',
                'Update info quote [' . $event->quote->uid . '] for expert failed! ' . print_r($result['errors'], true)
            );
        }
    }

}
