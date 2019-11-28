<?php

namespace sales\services\email\incoming;

use common\models\Client;
use common\models\ClientEmail;
use sales\entities\cases\Cases;
use sales\forms\lead\EmailCreateForm;
use sales\services\cases\CasesCreateService;
use sales\services\client\ClientManageService;

/**
 * Class EmailIncomingService
 *
 * @property CasesCreateService $casesCreateService
 * @property ClientManageService $clientManageService
 */
class EmailIncomingService
{
    private $casesCreateService;
    private $clientManageService;

    public function __construct(
        CasesCreateService $casesCreateService,
        ClientManageService $clientManageService
    )
    {
        $this->casesCreateService = $casesCreateService;
        $this->clientManageService = $clientManageService;
    }

    /**
     * @param string $clientEmail
     * @param int|null $projectId
     * @return int
     */
    public function getOrCreateCaseBySupport(string $clientEmail, ?int $projectId): ?int
    {
        $client = $this->clientManageService->getOrCreateByEmails([new EmailCreateForm(['email' => $clientEmail])]);

        if ($case = Cases::find()->findLastActiveSupportCaseByClient($client->id, $projectId)->one()) {
            return $case->cs_id;
        }

        try {
            $case = $this->casesCreateService->createSupportByIncomingEmail($client->id, $projectId);
            return $case->cs_id;
        } catch (\Throwable $e) {
            \Yii::error($e, 'EmailIncomingService:getOrCreateCaseBySupport');
            return null;
        }
    }
}
