<?php

namespace modules\order\src\services\confirmation;

use modules\order\src\entities\order\Order;
use modules\order\src\entities\orderData\OrderData;
use sales\services\cases\CasesCommunicationService;

class EmailConfirmationData
{
    public function generate(Order $order): array
    {
        $projectData = [];
        if ($order->or_project_id) {
            $project = $order->project;
            $projectContactInfo = [];
            if ($project->contact_info) {
                $projectContactInfo = @json_decode($project->contact_info, true);
            }
            $projectData = [
                'name'      => $project->name,
                'url'       => $project->link,
                'address'   => $projectContactInfo['address'] ?? '',
                'phone'     => $projectContactInfo['phone'] ?? '',
                'email'     => $projectContactInfo['email'] ?? '',
            ];
        }

        $languageId = null;
        $marketingCountry = null;
        $orderData = OrderData::find()->select(['od_language_id', 'od_market_country'])->byOrderId($order->or_id)->asArray()->one();
        if ($orderData) {
            $languageId = $orderData['od_language_id'];
            $marketingCountry = $orderData['od_market_country'];
        }
        $localeParams = CasesCommunicationService::getLocaleParams($order->or_project_id, $marketingCountry, $languageId);

        return [
            'project' => $projectData,
            'project_key' => $project->project_key ?? '',
            'order' => $order->serialize(),
            'content' => '',
            'subject' => '',
            'localeParams' => $localeParams,
        ];
    }
}
