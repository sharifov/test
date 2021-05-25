<?php

namespace sales\model\lead\useCases\lead\api\create;

use common\models\Client;
use common\models\ClientEmail;
use common\models\ClientPhone;
use common\models\Lead;
use common\models\LeadFlightSegment;
use sales\forms\lead\EmailCreateForm;
use sales\forms\lead\PhoneCreateForm;
use sales\helpers\setting\SettingHelper;
use sales\model\leadRequest\entity\LeadRequest;
use sales\model\leadRequest\service\LeadRequestDictionary;
use sales\model\leadRequest\service\LeadRequestService;
use sales\repositories\lead\LeadRepository;
use sales\repositories\lead\LeadSegmentRepository;
use sales\services\client\ClientCreateForm;
use sales\services\client\ClientManageService;
use sales\services\lead\calculator\LeadTripTypeCalculator;
use sales\services\lead\calculator\SegmentDTO;
use sales\services\lead\LeadHashGenerator;
use sales\services\TransactionManager;
use yii\helpers\ArrayHelper;

/**
 * Class LeadCreateGoogleRequest
 *
 * @property ClientManageService $clientManageService
 * @property TransactionManager $transactionManager
 * @property LeadHashGenerator $hashGenerator
 * @property LeadRepository $leadRepository
 */
class LeadCreateGoogleRequest
{
    private ClientManageService $clientManageService;
    private TransactionManager $transactionManager;
    private LeadHashGenerator $hashGenerator;
    private LeadRepository $leadRepository;

    /**
     * LeadCreateGoogleRequest constructor.
     * @param ClientManageService $clientManageService
     * @param TransactionManager $transactionManager
     * @param LeadHashGenerator $hashGenerator
     * @param LeadRepository $leadRepository
     */
    public function __construct(
        ClientManageService $clientManageService,
        TransactionManager $transactionManager,
        LeadHashGenerator $hashGenerator,
        LeadRepository $leadRepository
    ) {
        $this->clientManageService = $clientManageService;
        $this->transactionManager = $transactionManager;
        $this->hashGenerator = $hashGenerator;
        $this->leadRepository = $leadRepository;
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

            return $lead;
        });
    }
}
