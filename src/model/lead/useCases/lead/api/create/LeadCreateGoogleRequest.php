<?php

namespace src\model\lead\useCases\lead\api\create;

use common\models\Client;
use common\models\ClientEmail;
use common\models\ClientPhone;
use common\models\Lead;
use common\models\LeadFlightSegment;
use src\forms\lead\EmailCreateForm;
use src\forms\lead\PhoneCreateForm;
use src\helpers\setting\SettingHelper;
use src\model\leadRequest\entity\LeadRequest;
use src\model\leadRequest\repository\LeadRequestRepository;
use src\model\leadRequest\service\LeadRequestDictionary;
use src\model\leadRequest\service\LeadRequestService;
use src\repositories\lead\LeadRepository;
use src\repositories\lead\LeadSegmentRepository;
use src\services\client\ClientCreateForm;
use src\services\client\ClientManageService;
use src\services\lead\calculator\LeadTripTypeCalculator;
use src\services\lead\calculator\SegmentDTO;
use src\services\lead\LeadHashGenerator;
use src\services\TransactionManager;
use yii\helpers\ArrayHelper;

/**
 * Class LeadCreateGoogleRequest
 *
 * @property ClientManageService $clientManageService
 * @property TransactionManager $transactionManager
 * @property LeadHashGenerator $hashGenerator
 * @property LeadRepository $leadRepository
 * @property LeadRequestRepository $leadRequestRepository
 */
class LeadCreateGoogleRequest
{
    private ClientManageService $clientManageService;
    private TransactionManager $transactionManager;
    private LeadHashGenerator $hashGenerator;
    private LeadRepository $leadRepository;
    private LeadRequestRepository $leadRequestRepository;

    /**
     * LeadCreateGoogleRequest constructor.
     * @param ClientManageService $clientManageService
     * @param TransactionManager $transactionManager
     * @param LeadHashGenerator $hashGenerator
     * @param LeadRepository $leadRepository
     * @param LeadRequestRepository $leadRequestRepository
     */
    public function __construct(
        ClientManageService $clientManageService,
        TransactionManager $transactionManager,
        LeadHashGenerator $hashGenerator,
        LeadRepository $leadRepository,
        LeadRequestRepository $leadRequestRepository
    ) {
        $this->clientManageService = $clientManageService;
        $this->transactionManager = $transactionManager;
        $this->hashGenerator = $hashGenerator;
        $this->leadRepository = $leadRepository;
        $this->leadRequestRepository = $leadRequestRepository;
    }

    public function handle(LeadRequest $leadRequest): Lead
    {
        return $this->transactionManager->wrap(function () use ($leadRequest) {

            if (!$userColumnData = ArrayHelper::getValue($leadRequest, 'lr_json_data.user_column_data')) {
                throw new \DomainException('LeadRequest - user_column_data not found');
            }
            $email = LeadRequestService::findByColumnId(LeadRequestDictionary::COLUMN_EMAIL, $userColumnData);
            $phone = LeadRequestService::findByColumnId(LeadRequestDictionary::COLUMN_PHONE, $userColumnData);

            if (empty($email) && empty($phone)) {
                throw new \DomainException('Email or Phone required');
            }

            $client = $this->clientManageService->detectClient(
                $leadRequest->lr_project_id,
                null,
                $email,
                null,
                $phone
            );

            if (!$client) {
                $clientForm = ClientCreateForm::createWidthDefaultName();
                $clientForm->projectId = $leadRequest->lr_project_id;
                $clientForm->typeCreate = Client::TYPE_CREATE_LEAD;

                $client = $this->clientManageService->create($clientForm, null);
                if ($email) {
                    $this->clientManageService->addEmail($client, new EmailCreateForm(['email' => $email, 'type' => ClientEmail::EMAIL_NOT_SET]));
                }
                if ($phone) {
                    $this->clientManageService->addPhone($client, new PhoneCreateForm(['phone' => $phone, 'type' => ClientPhone::PHONE_NOT_SET]));
                }
            }

            $lead = Lead::createByApi();
            $lead->l_client_phone = $phone;
            $lead->project_id = $leadRequest->lr_project_id;
            $lead->source_id = $leadRequest->lr_source_id;
            $lead->cabin = Lead::CABIN_ECONOMY;
            $lead->adults = 1;
            $lead->client_id = $client->id;
            $lead->status = SettingHelper::getLeadApiGoogleStatusId();
            $lead->l_is_test = ArrayHelper::getValue($leadRequest, 'lr_json_data.is_test', false);
            $lead->l_dep_id = SettingHelper::getLeadApiGoogleDepartmentId();

            $hash = $this->hashGenerator->simple(
                $leadRequest->lr_project_id,
                $lead->adults,
                $lead->cabin,
                $client->id
            );

            $lead->setRequestHash($hash);

            if ($duplicate = $this->leadRepository->getByRequestHash($hash)) {
                $lead->status = null;
                $lead->duplicate($duplicate->id, null, null);
            }

            $this->leadRepository->save($lead);

            $leadRequest->setLeadId($lead->id);
            $this->leadRequestRepository->save($leadRequest);

            return $lead;
        });
    }
}
