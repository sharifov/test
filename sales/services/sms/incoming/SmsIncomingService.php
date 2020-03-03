<?php

namespace sales\services\sms\incoming;

use sales\entities\cases\Cases;
use sales\services\cases\CasesCommunicationService;
use sales\services\cases\CasesCreateService;
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

    public function __construct(
        ClientManageService $clients,
        LeadManageService $leadManageService,
        SmsRepository $smsRepository,
        TransactionManager $transactionManager,
        CasesCreateService $casesCreate,
        InternalContactService $internalContactService,
        CasesCommunicationService $casesCommunicationService
    )
    {
        $this->clients = $clients;
        $this->leadManageService = $leadManageService;
        $this->smsRepository = $smsRepository;
        $this->transactionManager = $transactionManager;
        $this->casesCreate = $casesCreate;
        $this->internalContactService = $internalContactService;
        $this->casesCommunicationService = $casesCommunicationService;
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

            $client = $this->clients->getOrCreateByPhones([new PhoneCreateForm(['phone' => $form->si_phone_from])]);

            $contact = $this->internalContactService->findByPhone($form->si_phone_to, $form->si_project_id);

            $form->replaceProject($contact->projectId);

            if (!$form->si_project_id) {
                throw new \DomainException('Incoming sms. Internal Phone: ' . $form->si_phone_to . '. Project Id not found');
            }

            if ($department = $contact->department) {
                if ($department->isSales()) {
                    $sms = $this->createSmsBySales($form, $client->id, $contact->userId);
                    $contact->releaseLog('Incoming sms. Internal Phone: ' . $form->si_phone_to . '. Sms Id: ' . $sms->s_id . ' | ', 'SmsIncomingService' );
                    return $sms;
                }
                if ($department->isExchange()) {
                    $sms = $this->createSmsByExchange($form, $client->id, $contact->userId);
                    $contact->releaseLog('Incoming sms. Internal Phone: ' . $form->si_phone_to . '. Sms Id: ' . $sms->s_id . ' | ', 'SmsIncomingService' );
                    return $sms;
                }
                if ($department->isSupport()) {
                    $sms = $this->createSmsBySupport($form, $client->id, $contact->userId);
                    $contact->releaseLog('Incoming sms. Internal Phone: ' . $form->si_phone_to . '. Sms Id: ' . $sms->s_id . ' | ', 'SmsIncomingService' );
                    return $sms;
                }
            }

            $sms = $this->createSmsByDefault($form, $client->id, $contact->userId);
            $contact->releaseLog('Incoming sms. Internal Phone: ' . $form->si_phone_to . '. Sms Id: ' . $sms->s_id . ' | ', 'SmsIncomingService' );
            Yii::error('Incoming sms. Sms Id: ' . $sms->s_id . ' | Not found Department for phone: ' . $form->si_phone_to, 'SmsIncomingService');
            return $sms;

        });

        return $sms;
    }

    /**
     * @param SmsIncomingForm $form
     * @param int $clientId
     * @param int|null $ownerId
     * @return Sms
     */
    private function createSmsBySales(SmsIncomingForm $form, int $clientId, ?int $ownerId): Sms
    {
        $leadId = null;
        if (!$lead = Lead::find()->findLastActiveSalesLeadByClient($clientId, $form->si_project_id)->one()) {
            if ((bool)Yii::$app->params['settings']['create_new_lead_sms']) {
                $lead = $this->leadManageService->createByIncomingSms(
                    $form->si_phone_from,
                    $clientId,
                    $form->si_project_id,
                    $this->findSource($form->si_project_id)
                );
                $leadId = $lead->id;
            } else {
                if ($lead = Lead::find()->findLastSalesLeadByClient($clientId, $form->si_project_id)->one()) {
                    $leadId = $lead->id;
                }
            }
        } else {
            $leadId = $lead->id;
        }
        $sms = Sms::createByIncomingSales($form, $clientId, $ownerId, $leadId);
        $this->smsRepository->save($sms);
        if ($leadId === null) {
            Yii::error('Incoming sms. Internal Phone: ' . $form->si_phone_to . '. Sms Id: ' . $sms->s_id . ' | No new lead creation allowed on SMS.', 'SmsIncomingService');
        }
        return $sms;
    }

    /**
     * @param SmsIncomingForm $form
     * @param int $clientId
     * @param int|null $ownerId
     * @return Sms
     */
    private function createSmsBySupport(SmsIncomingForm $form, int $clientId, ?int $ownerId): Sms
    {
        $caseId = null;
        if (!$case = Cases::find()->findLastActiveSupportCaseByClient($clientId, $form->si_project_id)->one()) {
            if ((bool)Yii::$app->params['settings']['create_new_support_case_sms']) {
                $case = $this->casesCreate->createSupportByIncomingSms(
                    $clientId,
                    $form->si_project_id
                );
                $caseId = $case->cs_id;
            } else {
                if ($case = Cases::find()->findLastSupportCaseByClient($clientId, $form->si_project_id)->one()) {
                    $caseId = $case->cs_id;
//                    $this->casesCommunicationService->processIncoming($case, CasesCommunicationService::TYPE_PROCESSING_SMS);
                }
            }
        } else {
            $caseId = $case->cs_id;
//            $this->casesCommunicationService->processIncoming($case, CasesCommunicationService::TYPE_PROCESSING_SMS);
        }
        $sms = Sms::createByIncomingSupport($form, $clientId, $ownerId, $caseId);
        $this->smsRepository->save($sms);
        if ($caseId === null) {
            Yii::error('Incoming sms. Internal Phone: ' . $form->si_phone_to . '. Sms Id: ' . $sms->s_id . ' | No new support case creation allowed on SMS.', 'SmsIncomingService');
        }
        return $sms;
    }

    /**
     * @param SmsIncomingForm $form
     * @param int $clientId
     * @param int|null $ownerId
     * @return Sms
     */
    private function createSmsByExchange(SmsIncomingForm $form, int $clientId, ?int $ownerId): Sms
    {
        $caseId = null;
        if (!$case = Cases::find()->findLastActiveExchangeCaseByClient($clientId, $form->si_project_id)->one()) {
            if ((bool)Yii::$app->params['settings']['create_new_exchange_case_sms']) {
                $case = $this->casesCreate->createExchangeByIncomingSms(
                    $clientId,
                    $form->si_project_id
                );
                $caseId = $case->cs_id;
            } else {
                if ($case = Cases::find()->findLastExchangeCaseByClient($clientId, $form->si_project_id)->one()) {
                    $caseId = $case->cs_id;
//                    $this->casesCommunicationService->processIncoming($case, CasesCommunicationService::TYPE_PROCESSING_SMS);
                }
            }
        } else {
            $caseId = $case->cs_id;
//            $this->casesCommunicationService->processIncoming($case, CasesCommunicationService::TYPE_PROCESSING_SMS);
        }
        $sms = Sms::createByIncomingExchange($form, $clientId, $ownerId, $caseId);
        $this->smsRepository->save($sms);
        if ($caseId === null) {
            Yii::error('Incoming sms. Internal Phone: ' . $form->si_phone_to . '. Sms Id: ' . $sms->s_id . ' | No new exchange case creation allowed on SMS.', 'SmsIncomingService');
        }
        return $sms;
    }

    /**
     * @param SmsIncomingForm $form
     * @param int $clientId
     * @param int|null $ownerId
     * @return Sms
     */
    private function createSmsByDefault(SmsIncomingForm $form, int $clientId, ?int $ownerId): Sms
    {
        $leadId = null;
        $caseId = null;
        if ($lead = Lead::find()->findLastActiveSalesLeadByClient($clientId, $form->si_project_id)->one()) {
            $leadId = $lead->id;
        } elseif ($case = Cases::find()->findLastActiveSupportCaseByClient($clientId, $form->si_project_id)->one()) {
            $caseId = $case->cs_id;
//            $this->casesCommunicationService->processIncoming($case, CasesCommunicationService::TYPE_PROCESSING_SMS);
        } elseif ($case = Cases::find()->findLastActiveExchangeCaseByClient($clientId, $form->si_project_id)->one()) {
            $caseId = $case->cs_id;
//            $this->casesCommunicationService->processIncoming($case, CasesCommunicationService::TYPE_PROCESSING_SMS);
        } else {
            return $this->createSmsBySupportDefault($form, $clientId, $ownerId);
        }

        $sms = Sms::createByIncomingDefault($form, $clientId, $ownerId, $leadId, $caseId);
        $this->smsRepository->save($sms);
        return $sms;
    }

    /**
     * @param SmsIncomingForm $form
     * @param int $clientId
     * @param int|null $ownerId
     * @return Sms
     */
    private function createSmsBySupportDefault(SmsIncomingForm $form, int $clientId, ?int $ownerId): Sms
    {
        $caseId = null;
        if ((bool)Yii::$app->params['settings']['create_new_support_case_sms']) {
            $case = $this->casesCreate->createSupportByIncomingSms(
                $clientId,
                $form->si_project_id
            );
            $caseId = $case->cs_id;
        } else {
            if ($case = Cases::find()->findLastSupportCaseByClient($clientId, $form->si_project_id)->one()) {
                $caseId = $case->cs_id;
//                $this->casesCommunicationService->processIncoming($case, CasesCommunicationService::TYPE_PROCESSING_SMS);
            }
        }
        $sms = Sms::createByIncomingSupport($form, $clientId, $ownerId, $caseId);
        $this->smsRepository->save($sms);
        if ($caseId === null) {
            Yii::error('Incoming sms. Internal Phone: ' . $form->si_phone_to . '. Sms Id: ' . $sms->s_id . ' | No new support case creation allowed on SMS.', 'SmsIncomingService');
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
