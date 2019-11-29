<?php

namespace sales\services\sms\incoming;

use sales\entities\cases\Cases;
use sales\services\cases\CasesCreateService;
use sales\services\contact\ContactService;
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
 * @property ContactService $contactService
 */
class SmsIncomingService
{
    private $clients;
    private $leadManageService;
    private $smsRepository;
    private $transactionManager;
    private $casesCreate;
    private $contactService;

    public function __construct(
        ClientManageService $clients,
        LeadManageService $leadManageService,
        SmsRepository $smsRepository,
        TransactionManager $transactionManager,
        CasesCreateService $casesCreate,
        ContactService $contactService
    )
    {
        $this->clients = $clients;
        $this->leadManageService = $leadManageService;
        $this->smsRepository = $smsRepository;
        $this->transactionManager = $transactionManager;
        $this->casesCreate = $casesCreate;
        $this->contactService = $contactService;
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

            $contact = $this->contactService->findByPhone($form->si_phone_to, $form->si_project_id);

            $form->replaceProject($contact->projectId);

            if ($department = $contact->department) {
                if ($department->isSales()) {
                    $sms = $this->createSmsBySales($form, $client->id, $contact->userId);
                    $contact->releaseLog('Incoming sms. Sms Id: ' . $sms->s_id, 'SmsIncomingService');
                    return $sms;
                }
                if ($department->isExchange()) {
                    $sms = $this->createSmsByExchange($form, $client->id, $contact->userId);
                    $contact->releaseLog('Incoming sms. Sms Id: ' . $sms->s_id, 'SmsIncomingService');
                    return $sms;
                }
                if ($department->isSupport()) {
                    $sms = $this->createSmsBySupport($form, $client->id, $contact->userId);
                    $contact->releaseLog('Incoming sms. Sms Id: ' . $sms->s_id, 'SmsIncomingService');
                    return $sms;
                }
            }

            $sms = $this->createSmsByDefault($form, $client->id, $contact->userId);
            Yii::error('Not found Department for phone: ' . $form->si_phone_to . '. Created sms Id: ' . $sms->s_id, 'SmsIncomingService');
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
        if (!$lead = Lead::find()->findLastActiveLeadByClient($clientId, $form->si_project_id)->one()) {
            if ((bool)Yii::$app->params['settings']['create_new_lead_sms']) {
                $lead = $this->leadManageService->createByIncomingSms(
                    $form->si_phone_from,
                    $clientId,
                    $form->si_project_id,
                    $this->findSource($form->si_project_id)
                );
                $leadId = $lead->id;
            }
        } else {
            $leadId = $lead->id;
        }
        $sms = Sms::createByIncomingSales($form, $clientId, $ownerId, $leadId);
        $this->smsRepository->save($sms);
        if ($leadId === null) {
            Yii::error('No new lead creation allowed on SMS. SMS Id: ' . $sms->s_id, 'SmsIncomingService');
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
            }
        } else {
            $caseId = $case->cs_id;
        }
        $sms = Sms::createByIncomingSupport($form, $clientId, $ownerId, $caseId);
        $this->smsRepository->save($sms);
        if ($caseId === null) {
            Yii::error('No new support case creation allowed on SMS. SMS Id: ' . $sms->s_id, 'SmsIncomingService');
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
            }
        } else {
            $caseId = $case->cs_id;
        }
        $sms = Sms::createByIncomingExchange($form, $clientId, $ownerId, $case->cs_id);
        $this->smsRepository->save($sms);
        if ($caseId === null) {
            Yii::error('No new exchange case creation allowed on SMS. SMS Id: ' . $sms->s_id, 'SmsIncomingService');
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
        if ($lead = Lead::find()->findLastActiveLeadByClient($clientId, $form->si_project_id)->one()) {
            $leadId = $lead->id;
        } elseif ($case = Cases::find()->findLastActiveSupportCaseByClient($clientId, $form->si_project_id)->one()) {
            $caseId = $case->cs_id;
        } elseif ($case = Cases::find()->findLastActiveExchangeCaseByClient($clientId, $form->si_project_id)->one()) {
            $caseId = $case->cs_id;
        }
        $sms = Sms::createByIncomingDefault($form, $clientId, $ownerId, $leadId, $caseId);
        $this->smsRepository->save($sms);
        return $sms;
    }

    /**
     * @param int|null $projectId
     * @return int|null
     */
    private function findSource(?int $projectId): ?int
    {
        if ($source = Sources::find()->select('id')->where(['project_id' => $projectId, 'default' => true])->one()) {
            return $source->id;
        }
        return null;
    }
}
