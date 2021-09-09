<?php

namespace sales\model\leadRedial\queue;

use common\models\Call;
use common\models\ClientPhone;
use common\models\Lead;
use frontend\widgets\redial\ClientPhonesDTO;

class ClientPhones
{
    /**
     * @param Lead $lead
     * @return ClientPhonesDTO[]
     */
    public function getPhones(Lead $lead): array
    {
        $phones = [];

        $lastCall = Call::find()
            ->andWhere(['c_lead_id' => $lead->id])
            ->andWhere(['c_call_type_id' => Call::CALL_TYPE_IN])
            ->andWhere(['IS NOT', 'c_from', null])
            ->orderBy(['c_updated_dt' => SORT_DESC])
            ->asArray()
            ->one();

        if ($lastCall) {
            $phones[] = new ClientPhonesDTO($lastCall['c_from'], 'Last Called');
        }

        if ($lead->l_client_phone) {
            $phones[] = new ClientPhonesDTO($lead->l_client_phone, 'Lead Phone');
        }

        if ($lead->client) {
            /** @var ClientPhone $phone */
            $clientPhones = $lead->client->getClientPhones()->
            andWhere(['or',
                ['type' => [ClientPhone::PHONE_FAVORITE, ClientPhone::PHONE_VALID, ClientPhone::PHONE_NOT_SET]],
                ['IS', 'type', null]
            ])
                ->orderBy(['type' => SORT_DESC])->asArray()->all();
            foreach ($clientPhones as $clientPhone) {
                $phones[] = new ClientPhonesDTO($clientPhone['phone']);
            }
        }

        return $phones;
    }
}
