<?php

namespace sales\services\sms\incoming;

use sales\entities\cases\Cases;
use sales\services\cases\CasesCreateService;
use Yii;
use sales\services\TransactionManager;
use common\models\Lead;
use common\models\Project;
use common\models\Sources;
use sales\repositories\sms\SmsRepository;
use sales\services\lead\LeadManageService;
use common\models\Department;
use common\models\DepartmentPhoneProject;
use common\models\Sms;
use common\models\UserProjectParams;
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
 */
class SmsIncomingService
{
    private $clients;
    private $leadManageService;
    private $smsRepository;
    private $transactionManager;
    private $casesCreate;

    public function __construct(
        ClientManageService $clients,
        LeadManageService $leadManageService,
        SmsRepository $smsRepository,
        TransactionManager $transactionManager,
        CasesCreateService $casesCreate
    )
    {
        $this->clients = $clients;
        $this->leadManageService = $leadManageService;
        $this->smsRepository = $smsRepository;
        $this->transactionManager = $transactionManager;
        $this->casesCreate = $casesCreate;
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

            $dpp = DepartmentPhoneProject::find()->findByPhone($form->si_phone_to);
            $upp = UserProjectParams::find()->findByPhone($form->si_phone_to);

            $department = $this->findDepartment($form->si_phone_to, $dpp, $upp);

            $project = $this->findProject($form->si_phone_to, $form->si_project_id, $dpp, $upp);
            $this->replaceOriginProject($form, $project);

            $ownerId = $upp ? $upp->upp_user_id : null;

            if ($department) {
                if ($department->isSales()) {
                    return $this->createSmsBySales($form, $client->id, $ownerId);
                }
                if ($department->isExchange()) {
                    return $this->createSmsByExchange($form, $client->id, $ownerId);
                }
                if ($department->isSupport()) {
                    return $this->createSmsBySupport($form, $client->id, $ownerId);
                }
            }

            $sms = $this->createSmsByDefault($form, $client->id, $ownerId);
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
    private function createSmsByDefault(SmsIncomingForm $form, int $clientId, ?int $ownerId): Sms
    {
        $sms = Sms::createByIncomingDefault($form, $clientId, $ownerId);
        $this->smsRepository->save($sms);
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
        if (!$case = Cases::find()->findLastActiveSupportCaseByClient($clientId)) {
            $case = $this->casesCreate->createSupportByIncomingSms(
                $clientId,
                $form->si_project_id
            );
        }
        $form->replaceProjectIfNotEqual($case->cs_project_id);
        $sms = Sms::createByIncomingSupport($form, $clientId, $ownerId, $case->cs_id);
        $this->smsRepository->save($sms);
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
        if (!$case = Cases::find()->findLastActiveExchangeCaseByClient($clientId)) {
            $case = $this->casesCreate->createExchangeByIncomingSms(
                $clientId,
                $form->si_project_id
            );
        }
        $form->replaceProjectIfNotEqual($case->cs_project_id);
        $sms = Sms::createByIncomingExchange($form, $clientId, $ownerId, $case->cs_id);
        $this->smsRepository->save($sms);
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
        if (!$lead = Lead::find()->findLastActiveLeadByClient($clientId)) {
            $lead = $this->leadManageService->createByIncomingSms(
                $form->si_phone_from,
                $clientId,
                $form->si_project_id,
                $this->findSource($form->si_project_id),
                Department::DEPARTMENT_SALES
            );
        }
        $form->replaceProjectIfNotEqual($lead->project_id);
        $sms = Sms::createByIncomingSales($form, $clientId, $ownerId, $lead->id);
        $this->smsRepository->save($sms);
        return $sms;
    }

    /**
     * @param SmsIncomingForm $form
     * @param Project|null $project
     */
    private function replaceOriginProject(SmsIncomingForm $form, ?Project $project): void
    {
        if (!$project) {
            return;
        }
        if (!$form->si_project_id) {
            $form->si_project_id = $project->id;
            return;
        }
        if ($project->id !== $form->si_project_id) {
            $form->si_project_id = $project->id;
            Yii::error('Project incoming and project found not equal. Incoming: ' . $form->si_project_id . '. Found: ' . $project->id, 'SmsIncomingService');
        }
    }

    /**
     * @param int|null $projectId
     * @return int|null
     */
    private function findSource(?int $projectId):? int
    {
        if ($source = Sources::find()->select('id')->where(['project_id' => $projectId, 'default' => true])->one()) {
            return $source->id;
        }
        return null;
    }

    /**
     * @param string $phone
     * @param DepartmentPhoneProject|null $dpp
     * @param UserProjectParams|null $upp
     * @return Department|null
     */
    private function findDepartment(string $phone, ?DepartmentPhoneProject $dpp, ?UserProjectParams $upp):? Department
    {
        if ($dpp) {
            if ($department = $dpp->dppDep) {
                return $department;
            }
            Yii::error('Not found department for departmentPhoneProject Id: ' . $dpp->dpp_id, 'SmsIncomingService');
        }
        if ($upp) {
            if ($department = $upp->uppDep) {
                return $department;
            }
            Yii::error('Not found department for userProjectParams tw_phone_number: ' . $upp->upp_tw_phone_number, 'SmsIncomingService');
            if ($upp->uppUser) {
                if ($upp->uppUser->userDepartments && isset($upp->uppUser->userDepartments[0]) && $upp->uppUser->userDepartments[0]->udDep) {
                    return $upp->uppUser->userDepartments[0]->udDep;
                }
                Yii::error('Not found department for user Id: ' . $upp->upp_user_id, 'SmsIncomingService');
            }
        }
        Yii::error('Not found department for phone: ' . $phone, 'SmsIncomingService');
        return null;
    }

    /**
     * @param string $phone
     * @param int|null $projectId
     * @param DepartmentPhoneProject|null $dpp
     * @param UserProjectParams|null $upp
     * @return Project|null
     */
    private function findProject(string $phone, ?int $projectId, ?DepartmentPhoneProject $dpp, ?UserProjectParams $upp):? Project
    {
        if ($projectId) {
            if ($project = Project::findOne($projectId)) {
                return $project;
            }
            Yii::error('Not found project for Id: ' . $projectId, 'SmsIncomingService');
        }
        if ($dpp) {
            if ($project = $dpp->dppProject) {
                return $project;
            }
            Yii::error('Not found project for departmentPhoneProject Id: ' . $dpp->dpp_id, 'SmsIncomingService');
        }
        if ($upp) {
            if ($project = $upp->uppProject) {
                return $project;
            }
            Yii::error('Not found project for userProjectParams tw_phone_number: ' . $upp->upp_tw_phone_number, 'SmsIncomingService');
        }
        Yii::error('Not found project for phone: ' . $phone, 'SmsIncomingService');
        return null;
    }
}
