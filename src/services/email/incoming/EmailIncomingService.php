<?php

namespace src\services\email\incoming;

use common\models\Client;
use common\models\Department;
use common\models\Project;
use common\models\Sources;
use modules\featureFlag\FFlag;
use src\model\leadBusinessExtraQueue\service\LeadBusinessExtraQueueService;
use src\model\leadBusinessExtraQueueLog\entity\LeadBusinessExtraQueueLogStatus;
use src\services\cases\CasesSaleService;
use src\services\client\ClientCreateForm;
use src\services\lead\LeadManageService;
use src\services\TransactionManager;
use Yii;
use common\models\Lead;
use src\entities\cases\Cases;
use src\forms\lead\EmailCreateForm;
use src\services\cases\CasesCreateService;
use src\services\client\ClientManageService;
use src\services\internalContact\InternalContactService;

/**
 * Class EmailIncomingService
 *
 * @property CasesCreateService $casesCreateService
 * @property ClientManageService $clientManageService
 * @property InternalContactService $internalContactService
 * @property LeadManageService $leadManageService
 * @property TransactionManager $transactionManager
 * @property CasesSaleService $casesSaleService
 */
class EmailIncomingService
{
    private $casesCreateService;
    private $clientManageService;
    private $internalContactService;
    private $leadManageService;
    private $transactionManager;
    private $casesSaleService;

    public function __construct(
        CasesCreateService $casesCreateService,
        ClientManageService $clientManageService,
        InternalContactService $internalContactService,
        LeadManageService $leadManageService,
        TransactionManager $transactionManager,
        CasesSaleService $casesSaleService
    ) {
        $this->casesCreateService = $casesCreateService;
        $this->clientManageService = $clientManageService;
        $this->internalContactService = $internalContactService;
        $this->leadManageService = $leadManageService;
        $this->transactionManager = $transactionManager;
        $this->casesSaleService = $casesSaleService;
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
    ): Process {
        /** @var Process $process */
        $process = $this->transactionManager->wrap(function () use ($clientEmail, $internalEmail, $incomingProject, $emailId) {
            $contact = $this->internalContactService->findByEmail($internalEmail, $incomingProject);

            if (!$contact->projectId) {
                throw new \DomainException('Incoming email. Internal Email: ' . $internalEmail . '. Created Email Id: ' . $emailId . ' | Project Id not found');
            }

            $clientForm = ClientCreateForm::createWidthDefaultName();
            $clientForm->projectId = $contact->projectId;
            $clientForm->typeCreate = Client::TYPE_CREATE_EMAIL;

            $client = $this->clientManageService->getOrCreateByEmails([new EmailCreateForm(['email' => $clientEmail])], $clientForm);

            if ($contact->department && ($departmentParams = $contact->department->getParams()) && $project = Project::findOne($contact->projectId)) {
                $projectParams = $project->getParams();

                if ($departmentParams->object->type->isLead()) {
                    $createLeadOnEmail = ($projectParams->object->lead->allow_auto_lead_create && $departmentParams->object->lead->isIncludeEmail($internalEmail));
                    $lead = $this->getOrCreateLead(
                        $client->id,
                        $clientEmail,
                        $contact->projectId,
                        $internalEmail,
                        $emailId,
                        $contact->department->dep_id,
                        $createLeadOnEmail
                    );
                    /** @fflag FFlag::FF_KEY_BEQ_ENABLE, Business Extra Queue enable */
                    if (\Yii::$app->featureFlag->isEnable(FFlag::FF_KEY_BEQ_ENABLE) && isset($lead) && $lead->isBusinessType()) {
                        LeadBusinessExtraQueueService::addLeadBusinessExtraQueueRemoverJob($lead->id, LeadBusinessExtraQueueLogStatus::REASON_RECEIVED_EMAIL);
                    }
                    $contact->releaseLog('Incoming email. Internal Email: ' . $internalEmail . '. Created Email Id: ' . $emailId . ' | ', 'EmailIncomingService');
                    return new Process($lead->id, null);
                }

                if ($departmentParams->object->type->isCase()) {
                    $createCaseOnEmail = ($projectParams->object->case->allow_auto_case_create && $departmentParams->object->case->isIncludeEmail($internalEmail));
                    $caseId = $this->getOrCreateCase(
                        $client->id,
                        $contact->projectId,
                        $internalEmail,
                        $emailId,
                        $contact->department->dep_id,
                        $createCaseOnEmail,
                        $departmentParams->object->case->trashActiveDaysLimit,
                    );
                    $contact->releaseLog('Incoming email. Internal Email: ' . $internalEmail . '. Created Email Id: ' . $emailId . ' | ', 'EmailIncomingService');
                    return new Process(null, $caseId);
                }
            }

            $contact->releaseLog('Incoming email. Internal Email: ' . $internalEmail . '. Created Email Id: ' . $emailId . ' | ', 'EmailIncomingService');
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
     * @param int $departmentId
     * @param bool $createLeadOnEmail
     * @return Lead|null
     */
    private function getOrCreateLead(
        int $clientId,
        string $clientEmail,
        ?int $projectId,
        string $internalEmail,
        int $emailId,
        int $departmentId,
        bool $createLeadOnEmail
    ): ?Lead {
        if ($lead = Lead::find()->findLastActiveLeadByDepartmentClient($departmentId, $clientId, $projectId)->one()) {
            return $lead->id;
        }
        if ($createLeadOnEmail) {
            $lead = $this->leadManageService->createByIncomingEmail(
                $clientEmail,
                $clientId,
                $projectId,
                $this->findSource($projectId),
                $departmentId
            );
            return $lead;
        }
        if ($lead = Lead::find()->findLastLeadByDepartmentClient($departmentId, $clientId, $projectId)->one()) {
            return $lead;
        }
//        Yii::info('Incoming email. Internal Email: ' . $internalEmail . '. Created Email Id: ' . $emailId . '. | No new lead creation allowed on email.', 'info\EmailIncomingService');
        return null;
    }

    private function getOrCreateCase(
        int $clientId,
        ?int $projectId,
        string $internalEmail,
        int $emailId,
        int $departmentId,
        bool $createCaseOnEmail,
        int $trashActiveDaysLimit
    ): ?int {
        if ($case = Cases::find()->findLastActiveClientCaseByDepartment($departmentId, $clientId, $projectId, $trashActiveDaysLimit)->one()) {
            return $case->cs_id;
        }
        if ($createCaseOnEmail) {
            $case = $this->casesCreateService->createByDepartmentIncomingEmail($departmentId, $clientId, $projectId);
            return $case->cs_id;
        }
        if ($case = Cases::find()->findLastClientCaseByDepartment($departmentId, $clientId, $projectId)->one()) {
            return $case->cs_id;
        }
//        Yii::info('Incoming email. Internal Email: ' . $internalEmail . '. Created Email Id: ' . $emailId . '. | No new exchange case creation allowed on Email.', 'info\EmailIncomingService');
        return null;
    }

    private function getOrCreateByDefault(
        int $clientId,
        ?int $projectId,
        string $internalEmail,
        int $emailId
    ): Process {
        $leadId = null;
        $caseId = null;
        if ($lead = Lead::find()->findLastActiveSalesLeadByClient($clientId, $projectId)->one()) {
            $leadId = $lead->id;
        } else {
            $department = Department::find()->andWhere(['dep_id' => Department::DEPARTMENT_SUPPORT])->one();
            if (
                $department
                && ($departmentParams = $department->getParams())
                && $case = Cases::find()->findLastActiveClientCaseByDepartment($department->dep_id, $clientId, $projectId, $departmentParams->object->case->trashActiveDaysLimit)->one()
            ) {
                $caseId = $case->cs_id;
            } else {
                $department = Department::find()->andWhere(['dep_id' => Department::DEPARTMENT_EXCHANGE])->one();
                if (
                    $department
                    && ($departmentParams = $department->getParams())
                    && $case = Cases::find()->findLastActiveClientCaseByDepartment($department->dep_id, $clientId, $projectId, $departmentParams->object->case->trashActiveDaysLimit)->one()
                ) {
                    $caseId = $case->cs_id;
                } else {
                    if (isset($departmentParams) && $departmentParams->object->case->isIncludeEmail($internalEmail)) {
                        $case = $this->casesCreateService->createByDepartmentIncomingEmail(Department::DEPARTMENT_SUPPORT, $clientId, $projectId);
                        return new Process($leadId, $case->cs_id);
                    }
                    if ($case = Cases::find()->findLastClientCaseByDepartment(Department::DEPARTMENT_SUPPORT, $clientId, $projectId)->one()) {
                        return new Process($leadId, $case->cs_id);
                    }
//                    Yii::info('Incoming email. Internal Email: ' . $internalEmail . '. Created Email Id: ' . $emailId . '. | No new support case creation allowed on Email.', 'info\EmailIncomingService');
                }
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
