<?php

namespace sales\services\sms\incoming;

use common\models\Client;
use common\models\Department;
use sales\dispatchers\EventDispatcher;
use sales\entities\cases\Cases;
use sales\model\phoneList\entity\PhoneList;
use sales\services\cases\CasesCommunicationService;
use sales\services\cases\CasesCreateService;
use sales\services\client\ClientCreateForm;
use sales\services\internalContact\InternalContactService;
use Yii;
use sales\services\TransactionManager;
use common\models\Lead;
use common\models\Sources;
use sales\repositories\sms\SmsRepository;
use sales\services\lead\LeadManageService;
use common\models\Sms;
use sales\forms\lead\PhoneCreateForm;
use sales\services\client\ClientManageService;

/**
 * Class SmsIncomingService
 *
 * @property ClientManageService $clients
 * @property LeadManageService $leadManageService
 * @property SmsRepository $smsRepository
 * @property TransactionManager $transactionManager
 * @property CasesCreateService $casesCreate
 * @property InternalContactService $internalContactService
 * @property CasesCommunicationService $casesCommunicationService
 * @property EventDispatcher $eventDispatcher
 */
class SmsIncomingService
{
    private $clients;
    private $leadManageService;
    private $smsRepository;
    private $transactionManager;
    private $casesCreate;
    private $internalContactService;
    private $casesCommunicationService;
    private $eventDispatcher;

    public function __construct(
        ClientManageService $clients,
        LeadManageService $leadManageService,
        SmsRepository $smsRepository,
        TransactionManager $transactionManager,
        CasesCreateService $casesCreate,
        InternalContactService $internalContactService,
        CasesCommunicationService $casesCommunicationService,
        EventDispatcher $eventDispatcher
    ) {
        $this->clients = $clients;
        $this->leadManageService = $leadManageService;
        $this->smsRepository = $smsRepository;
        $this->transactionManager = $transactionManager;
        $this->casesCreate = $casesCreate;
        $this->internalContactService = $internalContactService;
        $this->casesCommunicationService = $casesCommunicationService;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @param SmsIncomingForm $form
     * @return Sms
     * @throws \Throwable
     */
    public function create(SmsIncomingForm $form): Sms
    {
        /** @var Sms $sms */
        $sms = $this->transactionManager->wrap(function () use ($form) {
            $isInternalPhone = PhoneList::find()->byPhone($form->si_phone_from)->enabled()->exists();

            $clientForm = ClientCreateForm::createWidthDefaultName();
            $clientForm->projectId = $form->si_project_id;
            $clientForm->typeCreate = Client::TYPE_CREATE_SMS;

            if (!$isInternalPhone) {
                $client = $this->clients->getOrCreateByPhones([new PhoneCreateForm(['phone' => $form->si_phone_from])], $clientForm);
            } else {
                $client = $this->clients->getExistingOrCreateEmptyObj([new PhoneCreateForm(['phone' => $form->si_phone_from])], $clientForm);
            }

            $contact = $this->internalContactService->findByPhone($form->si_phone_to, $form->si_project_id);

            $form->replaceProject($contact->projectId);

            if (!$form->si_project_id) {
                throw new \DomainException('Incoming sms. Internal Phone: ' . $form->si_phone_to . '. Project Id not found');
            }

            if ($contact->department && ($departmentParams = $contact->department->getParams())) {
                if ($departmentParams->object->type->isLead()) {
                    $sms = $this->createSmsByLeadType(
                        $form,
                        $client->id,
                        $contact->userId,
                        $isInternalPhone,
                        $contact->department->dep_id,
                        $departmentParams->object->lead->createOnSms
                    );
                    $contact->releaseLog('Incoming sms. Internal Phone: ' . $form->si_phone_to . '. Sms Id: ' . $sms->s_id . ' | ', 'SmsIncomingService');
                    $this->eventDispatcher->dispatch(new SmsIncomingEvent($sms));
                    return $sms;
                }

                if ($departmentParams->object->type->isCase()) {
                    $sms = $this->createSmsByCaseType(
                        $form,
                        $client->id,
                        $contact->userId,
                        $isInternalPhone,
                        $contact->department->dep_id,
                        $departmentParams->object->case->createOnSms,
                        $departmentParams->object->case->trashActiveDaysLimit
                    );
                    $contact->releaseLog('Incoming sms. Internal Phone: ' . $form->si_phone_to . '. Sms Id: ' . $sms->s_id . ' | ', 'SmsIncomingService');
                    $this->eventDispatcher->dispatch(new SmsIncomingEvent($sms));
                    return $sms;
                }
            }

            $sms = $this->createSmsByDefault($form, $client->id, $contact->userId, $isInternalPhone);
            $contact->releaseLog('Incoming sms. Internal Phone: ' . $form->si_phone_to . '. Sms Id: ' . $sms->s_id . ' | ', 'SmsIncomingService');
            Yii::error('Incoming sms. Sms Id: ' . $sms->s_id . ' | Not found Department for phone: ' . $form->si_phone_to, 'SmsIncomingService');

            $this->eventDispatcher->dispatch(new SmsIncomingEvent($sms));
            return $sms;
        });

        return $sms;
    }

    private function createSmsByLeadType(
        SmsIncomingForm $form,
        int $clientId,
        ?int $ownerId,
        bool $isInternalPhone,
        int $departmentId,
        bool $createLeadOnSms
    ): Sms {
        $leadId = null;
        if (!$lead = Lead::find()->findLastActiveLeadByDepartmentClient($departmentId, $clientId, $form->si_project_id)->one()) {
            if ($createLeadOnSms && !$isInternalPhone) {
                $lead = $this->leadManageService->createByIncomingSms(
                    $form->si_phone_from,
                    $clientId,
                    $form->si_project_id,
                    $this->findSource($form->si_project_id),
                    $departmentId
                );
                $leadId = $lead->id;
            } else {
                if ($lead = Lead::find()->findLastLeadByDepartmentClient($departmentId, $clientId, $form->si_project_id)->one()) {
                    $leadId = $lead->id;
                }
            }
        } else {
            $leadId = $lead->id;
        }
        $sms = Sms::createIncomingByLeadType($form, $clientId ?: null, $ownerId, $leadId);
        $this->smsRepository->save($sms);
        if ($leadId === null) {
//            Yii::info('Incoming sms. Internal Phone: ' . $form->si_phone_to . '. Sms Id: ' . $sms->s_id . ' | No new lead creation allowed on SMS.', 'info\SmsIncomingService');
        }
        return $sms;
    }

    private function createSmsByCaseType(
        SmsIncomingForm $form,
        int $clientId,
        ?int $ownerId,
        bool $isInternalPhone,
        int $departmentId,
        bool $createCaseOnSms,
        int $trashActiveDaysLimit
    ): Sms {
        $caseId = null;
        if (!$case = Cases::find()->findLastActiveClientCaseByDepartment($departmentId, $clientId, $form->si_project_id, $trashActiveDaysLimit)->one()) {
            if ($createCaseOnSms && !$isInternalPhone) {
                $case = $this->casesCreate->createByIncomingSms(
                    $departmentId,
                    $clientId,
                    $form->si_project_id
                );
                $caseId = $case->cs_id;
            } else {
                if ($case = Cases::find()->findLastClientCaseByDepartment($departmentId, $clientId, $form->si_project_id)->one()) {
                    $caseId = $case->cs_id;
//                    $this->casesCommunicationService->processIncoming($case, CasesCommunicationService::TYPE_PROCESSING_SMS);
                }
            }
        } else {
            $caseId = $case->cs_id;
//            $this->casesCommunicationService->processIncoming($case, CasesCommunicationService::TYPE_PROCESSING_SMS);
        }
        $sms = Sms::createIncomingByCaseType($form, $clientId ?: null, $ownerId, $caseId);
        $this->smsRepository->save($sms);
        if ($caseId === null) {
//            Yii::info('Incoming sms. Internal Phone: ' . $form->si_phone_to . '. Sms Id: ' . $sms->s_id . ' | No new exchange case creation allowed on SMS.', 'info\SmsIncomingService');
        }
        return $sms;
    }

    /**
     * @param SmsIncomingForm $form
     * @param int $clientId
     * @param int|null $ownerId
     * @param bool $isInternalPhone
     * @return Sms
     */
    private function createSmsByDefault(SmsIncomingForm $form, int $clientId, ?int $ownerId, bool $isInternalPhone = false): Sms
    {
        $leadId = null;
        $caseId = null;
        if ($lead = Lead::find()->findLastActiveSalesLeadByClient($clientId, $form->si_project_id)->one()) {
            $leadId = $lead->id;
        } else {
            $department = Department::find()->andWhere(['dep_id' => Department::DEPARTMENT_SUPPORT])->one();
            if (
                $department
                && ($departmentParams = $department->getParams())
                && $case = Cases::find()->findLastActiveClientCaseByDepartment($department->dep_id, $clientId, $form->si_project_id, $departmentParams->object->case->trashActiveDaysLimit)->one()
            ) {
                $caseId = $case->cs_id;
            } else {
                $department = Department::find()->andWhere(['dep_id' => Department::DEPARTMENT_EXCHANGE])->one();
                if (
                    $department
                    && ($departmentParams = $department->getParams())
                    && $case = Cases::find()->findLastActiveClientCaseByDepartment($department->dep_id, $clientId, $form->si_project_id, $departmentParams->object->case->trashActiveDaysLimit)->one()
                ) {
                    $caseId = $case->cs_id;
                } else {
                    return $this->createSmsBySupportDefault($form, $clientId, $ownerId, $isInternalPhone);
                }
            }
        }

        $sms = Sms::createByIncomingDefault($form, $clientId ?: null, $ownerId, $leadId, $caseId);
        $this->smsRepository->save($sms);
        return $sms;
    }

    /**
     * @param SmsIncomingForm $form
     * @param int $clientId
     * @param int|null $ownerId
     * @param bool $isInternalPhone
     * @return Sms
     */
    private function createSmsBySupportDefault(SmsIncomingForm $form, int $clientId, ?int $ownerId, bool $isInternalPhone = false): Sms
    {
        $depId = Department::DEPARTMENT_SUPPORT;
        $caseId = null;
        $department = Department::find()->andWhere(['dep_id' => $depId])->one();
        if (!$department) {
            throw new \DomainException('Not found Department. ID: ' . $depId);
        }
        if (!$departmentParams = $department->getParams()) {
            throw new \DomainException('Not found Department Params. ID: ' . $depId);
        }
        if ($departmentParams->object->case->createOnSms && !$isInternalPhone) {
            $case = $this->casesCreate->createByIncomingSms(
                $department->dep_id,
                $clientId,
                $form->si_project_id
            );
            $caseId = $case->cs_id;
        } else {
            if ($case = Cases::find()->findLastClientCaseByDepartment($department->dep_id, $clientId, $form->si_project_id)->one()) {
                $caseId = $case->cs_id;
//                $this->casesCommunicationService->processIncoming($case, CasesCommunicationService::TYPE_PROCESSING_SMS);
            }
        }
        $sms = Sms::createIncomingByCaseType($form, $clientId ?: null, $ownerId, $caseId);
        $this->smsRepository->save($sms);
        if ($caseId === null) {
//            Yii::info('Incoming sms. Internal Phone: ' . $form->si_phone_to . '. Sms Id: ' . $sms->s_id . ' | No new support case creation allowed on SMS.', 'info\SmsIncomingService');
        }
        return $sms;
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
