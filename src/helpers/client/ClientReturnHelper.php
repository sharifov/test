<?php

namespace src\helpers\client;

use src\model\clientData\entity\ClientDataQuery;
use src\model\clientDataKey\entity\ClientDataKeyDictionary;
use src\model\clientDataKey\service\ClientDataKeyService;
use src\model\clientUserReturn\entity\ClientUserReturnDictionary;
use src\model\clientUserReturn\entity\ClientUserReturnQuery;
use yii\helpers\Html;

class ClientReturnHelper
{
    public static function displayClientReturnLabels(int $clientId, int $userId): string
    {
        $labels = [];
        $clientDataKeyId = ClientDataKeyService::getIdByKey(ClientDataKeyDictionary::CLIENT_RETURN);
        if ($clientDataKeyId) {
            $clientData = ClientDataQuery::findByClientAndKeyId($clientId, $clientDataKeyId);
            $icon = Html::tag('i', '', ['class' => 'fa fa-tag']);
            foreach ($clientData as $value) {
                $title = 'Return Client Type: ' . $value->cd_field_value;
                $options = ['class' => 'label label-info', 'title' => $title];
                if ($value->isClientReturn()) {
                    if (ClientUserReturnQuery::exists($clientId, $userId)) {
                        $labels[] = Html::tag('span', $icon . ' ' . ClientUserReturnDictionary::AGENT_RETURN, ['class' => 'label label-warning', 'title' => $title]);
                    } else {
                        $labels[] = Html::tag('span', $icon . ' ' . ClientUserReturnDictionary::COMPANY_RETURN, $options);
                    }
                } else {
                    $labels[] = Html::tag('span', $icon . ' ' . $value->cd_field_value_ui ?? $value->cd_field_value, $options);
                }
            }
        }
        return implode(' ', $labels);
    }
}
