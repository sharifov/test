<?php

namespace sales\services\email\incoming;

use common\models\Client;
use common\models\ClientEmail;
use sales\entities\cases\Cases;
use sales\services\cases\CasesCreateService;

/**
 * Class EmailIncomingService
 *
 * @property CasesCreateService $casesCreateService
 */
class EmailIncomingService
{
    private $casesCreateService;

    public function __construct(CasesCreateService $casesCreateService)
    {
        $this->casesCreateService = $casesCreateService;
    }

    /**
     * @param string $clientEmail
     * @param int|null $projectId
     * @return int
     */
    public function getOrCreate(string $clientEmail, ?int $projectId): ?int
    {
        if (!$client = $this->findClientByEmail($clientEmail)) {
            return null;
        }

        if ($case = Cases::find()->findLastActiveCaseByClient($client->id)->one()) {
            return $case->cs_id;
        }

        try {
            $case = $this->casesCreateService->createSupportByIncomingEmail($client->id, $projectId);
            return $case->cs_id;
        } catch (\Throwable $e) {
            \Yii::error($e, 'EmailIncomingService:getOrCreate');
            return null;
        }
    }

    /**
     * @param $email
     * @return Client|null
     */
    private function findClientByEmail($email): ?Client
    {
        $email = ClientEmail::find()->andWhere(['email' => $email])->orderBy(['created' => SORT_DESC])->limit(1)->one();
        if ($client = $email->client) {
            return $client;
        }
        \Yii::error('ClientEmail found but Client with email: ' . $email . ' not found.');
        return null;
    }
}
