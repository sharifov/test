<?php

namespace sales\services\email\incoming;

use common\models\Client;
use common\models\Department;
use common\models\Sources;
use sales\services\cases\CasesSaleService;
use sales\services\client\ClientCreateForm;
use sales\services\lead\LeadManageService;
use sales\services\TransactionManager;
use Yii;
use common\models\Lead;
use sales\entities\cases\Cases;
use sales\forms\lead\EmailCreateForm;
use sales\services\cases\CasesCreateService;
use sales\services\client\ClientManageService;
use sales\services\internalContact\InternalContactService;

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

            if ($contact->department && ($departmentParams = $contact->department->getParams())) {
                if ($departmentParams->object->type->isLead()) {
                    $leadId = $this->getOrCreateLead(
                        $client->id,
                        $clientEmail,
                        $contact->projectId,
                        $internalEmail,
                        $emailId,
                        $contact->department->dep_id,
                        $departmentParams->object->lead->createOnEmail
                    );
                    $contact->releaseLog('Incoming email. Internal Email: ' . $internalEmail . '. Created Email Id: ' . $emailId . ' | ', 'EmailIncomingService');
                    return new Process($leadId, null);
                }

                if ($departmentParams->object->type->isCase()) {
                    $caseId = $this->getOrCreateCase(
                        $client->id,
                        $contact->projectId,
                        $internalEmail,
                        $emailId,
                        $contact->department->dep_id,
                        $departmentParams->object->case->createOnEmail,
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
     * @return int|null
     */
    private function getOrCreateLead(
        int $clientId,
        string $clientEmail,
        ?int $projectId,
        string $internalEmail,
        int $emailId,
        int $departmentId,
        bool $createLeadOnEmail
    ): ?int {
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
            return $lead->id;
        }
        if ($lead = Lead::find()->findLastLeadByDepartmentClient($departmentId, $clientId, $projectId)->one()) {
            return $lead->id;
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
                    if ((bool)Yii::$app->params['settings']['create_case_only_department_email'] === false) {
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
