<?php

namespace sales\services\email\incoming;

use common\models\Sources;
use sales\services\lead\LeadManageService;
use sales\services\TransactionManager;
use Yii;
use common\models\Lead;
use sales\entities\cases\Cases;
use sales\forms\lead\EmailCreateForm;
use sales\services\cases\CasesCreateService;
use sales\services\client\ClientManageService;
use sales\services\internalContact\InternalContactService;
use yii\helpers\VarDumper;

/**
 * Class EmailIncomingService
 *
 * @property CasesCreateService $casesCreateService
 * @property ClientManageService $clientManageService
 * @property InternalContactService $internalContactService
 * @property LeadManageService $leadManageService
 * @property TransactionManager $transactionManager
 */
class EmailIncomingService
{
    private $casesCreateService;
    private $clientManageService;
    private $internalContactService;
    private $leadManageService;
    private $transactionManager;

    public function __construct(
        CasesCreateService $casesCreateService,
        ClientManageService $clientManageService,
        InternalContactService $internalContactService,
        LeadManageService $leadManageService,
        TransactionManager $transactionManager
    )
    {
        $this->casesCreateService = $casesCreateService;
        $this->clientManageService = $clientManageService;
        $this->internalContactService = $internalContactService;
        $this->leadManageService = $leadManageService;
        $this->transactionManager = $transactionManager;
    }

    /**
     * @param int $emailId
     * @param string $clientEmail
     * @param string $internalEmail
     * @param int|null $incomingProject
     * @return Process
     * @throws \Throwable
     */
    public function create(
        int $emailId,
        string $clientEmail,
        string $internalEmail,
        ?int $incomingProject
    ): Process
    {
        /** @var Process $process */
        $process = $this->transactionManager->wrap(function () use ($clientEmail, $internalEmail, $incomingProject, $emailId) {

            $client = $this->clientManageService->getOrCreateByEmails([new EmailCreateForm(['email' => $clientEmail])]);

            $contact = $this->internalContactService->findByEmail($internalEmail, $incomingProject);

            if (!$contact->projectId) {
                throw new \DomainException('Incoming email. Internal Email: ' . $internalEmail . '. Created Email Id: ' . $emailId . ' | Project Id not found');
            }

            if ($department = $contact->department) {
                if ($department->isSales()) {
                    $leadId = $this->getOrCreateLead(
                        $client->id,
                        $clientEmail,
                        $contact->projectId,
                        $internalEmail,
                        $emailId
                    );
                    $contact->releaseLog('Incoming email. Internal Email: ' . $internalEmail . '. Created Email Id: ' . $emailId . ' | ', 'EmailIncomingService' );
                    return new Process($leadId, null);
                }
                if ($department->isExchange()) {
                    $caseId = $this->getOrCreateCaseByExchange($client->id, $contact->projectId, $internalEmail, $emailId);
                    $contact->releaseLog('Incoming email. Internal Email: ' . $internalEmail . '. Created Email Id: ' . $emailId . ' | ', 'EmailIncomingService' );
                    return new Process(null, $caseId);
                }
                if ($department->isSupport()) {
                    $caseId = $this->getOrCreateCaseBySupport($client->id, $contact->projectId, $internalEmail, $emailId);
                    $contact->releaseLog('Incoming email. Internal Email: ' . $internalEmail . '. Created Email Id: ' . $emailId . ' | ', 'EmailIncomingService' );
                    return new Process(null, $caseId);
                }
            }

            $contact->releaseLog('Incoming email. Internal Email: ' . $internalEmail . '. Created Email Id: ' . $emailId . ' | ', 'EmailIncomingService' );
            Yii::error('Incoming email. Created Email Id: ' . $emailId . ' | Not found Department for email: ' . $internalEmail, 'EmailIncomingService');
            $process = $this->getOrCreateByDefault($client->id, $contact->projectId, $internalEmail, $emailId);
            return $process;

        });

        return $process;
    }

    /**
     * @param int $clientId
     * @param string $clientEmail
     * @param int|null $projectId
     * @param string $internalEmail
     * @param int $emailId
     * @return int|null
     */
    private function getOrCreateLead(int $clientId, string $clientEmail, ?int $projectId, string $internalEmail, int $emailId): ?int
    {
        if ($lead = Lead::find()->findLastActiveSalesLeadByClient($clientId, $projectId)->one()) {
            return $lead->id;
        }
        if ((bool)Yii::$app->params['settings']['create_new_lead_email']) {
            $lead = $this->leadManageService->createByIncomingEmail(
                $clientEmail,
                $clientId,
                $projectId,
                $this->findSource($projectId)
            );
            return $lead->id;
        }
        Yii::warning('Incoming email. Internal Email: ' . $internalEmail . '. Created Email Id: ' . $emailId . '. | No new lead creation allowed on email.', 'EmailIncomingService');
        return null;
    }

    /**
     * @param int $clientId
     * @param int|null $projectId
     * @param string $internalEmail
     * @param int $emailId
     * @return int|null
     */
    private function getOrCreateCaseByExchange(int $clientId, ?int $projectId, string $internalEmail, int $emailId): ?int
    {
        if ($case = Cases::find()->findLastActiveExchangeCaseByClient($clientId, $projectId)->one()) {
            return $case->cs_id;
        }
        if ((bool)Yii::$app->params['settings']['create_new_exchange_case_email']) {
            $case = $this->casesCreateService->createExchangeByIncomingEmail($clientId, $projectId);
            return $case->cs_id;
        }
        Yii::warning('Incoming email. Internal Email: ' . $internalEmail . '. Created Email Id: ' . $emailId . '. | No new exchange case creation allowed on Email.', 'EmailIncomingService');
        return null;
    }

    /**
     * @param int $clientId
     * @param int|null $projectId
     * @param string $internalEmail
     * @param int $emailId
     * @return int|null
     */
    private function getOrCreateCaseBySupport(int $clientId, ?int $projectId, string $internalEmail, int $emailId): ?int
    {
        if ($case = Cases::find()->findLastActiveSupportCaseByClient($clientId, $projectId)->one()) {
            return $case->cs_id;
        }
        if ((bool)Yii::$app->params['settings']['create_new_support_case_email']) {
            $case = $this->casesCreateService->createSupportByIncomingEmail($clientId, $projectId);
            return $case->cs_id;
        }
        Yii::warning('Incoming email. Internal Email: ' . $internalEmail . '. Created Email Id: ' . $emailId . '. | No new support case creation allowed on Email.', 'EmailIncomingService');
        return null;
    }

    /**
     * @param int $clientId
     * @param int|null $projectId
     * @param string $internalEmail
     * @param int $emailId
     * @return Process
     */
    private function getOrCreateByDefault(int $clientId, ?int $projectId, string $internalEmail, int $emailId): Process
    {
        $leadId = null;
        $caseId = null;
        if ($lead = Lead::find()->findLastActiveSalesLeadByClient($clientId, $projectId)->one()) {
            $leadId = $lead->id;
        } elseif ($case = Cases::find()->findLastActiveSupportCaseByClient($clientId, $projectId)->one()) {
            $caseId = $case->cs_id;
        } elseif ($case = Cases::find()->findLastActiveExchangeCaseByClient($clientId, $projectId)->one()) {
            $caseId = $case->cs_id;
        } else {
            if ((bool)Yii::$app->params['settings']['create_new_support_case_email']) {
                $case = $this->casesCreateService->createSupportByIncomingEmail($clientId, $projectId);
                return new Process(null, $case->cs_id);
            } else {
                Yii::warning('Incoming email. Internal Email: ' . $internalEmail . '. Created Email Id: ' . $emailId . '. | No new support case creation allowed on Email.', 'EmailIncomingService');
            }
        }

        return new Process($leadId, $caseId);
    }

    /**
     * @param int|null $projectId
     * @return int|null
     */
    private function findSource(?int $projectId): ?int
    {
        if ($projectId === null) {
            return null;
        }
        if ($source = Sources::find()->select('id')->where(['project_id' => $projectId, 'default' => true])->one()) {
            return $source->id;
        }
        return null;
    }
}
