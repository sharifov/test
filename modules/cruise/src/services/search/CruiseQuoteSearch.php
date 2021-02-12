<?php

namespace modules\cruise\src\services\search;

class CruiseQuoteSearch
{
    private $service;

    public function __construct()
    {
        $this->service = \Yii::$app->communication;
    }

    public function search(Params $params): array
    {
        $data = [
            'date_from' => $params->date_from,
            'date_to' => $params->date_to,
            'destination' => $params->destination,
            'adult' => $params->adult,
            'child' => $params->child
        ];
        $result = $this->service->cruiseSearch($data);
        if ($result['error'] === false) {
            return $result['data'] ?? [];
        }
        throw new \DomainException($result['error']);
    }
}
