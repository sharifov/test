<?php

namespace src\services\cases;

use common\models\Client;
use common\models\DepartmentPhoneProject;
use modules\experiment\models\ExperimentTarget;
use modules\order\src\entities\order\Order;
use modules\order\src\entities\orderContact\OrderContact;
use modules\order\src\entities\orderData\OrderData;
use src\entities\cases\CaseCategory;
use src\entities\cases\CaseEventLog;
use src\entities\cases\Cases;
use src\forms\cases\CasesCreateByChatForm;
use src\forms\cases\CasesCreateByWebForm;
use src\forms\lead\EmailCreateForm;
use src\forms\lead\PhoneCreateForm;
use src\helpers\app\AppHelper;
use src\helpers\setting\SettingHelper;
use src\model\caseOrder\entity\CaseOrder;
use src\model\clientChat\entity\ClientChat;
use src\model\clientChatCase\entity\ClientChatCase;
use src\model\clientChatCase\entity\ClientChatCaseRepository;
use src\repositories\cases\CasesRepository;
use src\repositories\NotFoundException;
use src\services\client\ClientCreateForm;
use src\services\client\ClientManageService;
use src\services\departmentPhoneProject\DepartmentPhoneProjectParamsService;
use src\services\TransactionManager;
use yii\helpers\VarDumper;

/**
 * Class CasesCreateService
 *
 * @property CasesRepository $casesRepository
 * @property ClientManageService $clientManageService
 * @property TransactionManager $transaction
 * @property CasesSaleService $casesSaleService
 * @property ClientChatCaseRepository $chatCaseRepository
 */
class CasesCreateService
{
    private $casesRepository;
    private $clientManageService;
    private $transaction;
    private $casesSaleService;
    private $chatCaseRepository;

    public function __construct(
        CasesRepository $casesRepository,
        ClientManageService $clientManageService,
        TransactionManager $transaction,
        CasesSaleService $casesSaleService,
        ClientChatCaseRepository $chatCaseRepository
    ) {
        $this->casesRepository = $casesRepository;
        $this->clientManageService = $clientManageService;
        $this->transaction = $transaction;
        $this->casesSaleService = $casesSaleService;
        $this->chatCaseRepository = $chatCaseRepository;
    }

    public function createByDepartmentIncomingEmail(int $departmentId, int $clientId, ?int $projectId): Cases
    {
        $case = Cases::createByDepartmentIncomingEmail($departmentId, $clientId, $projectId);
        $this->casesRepository->save($case);
        return $case;
    }

    public function createByIncomingSms(int $departmentId, int $clientId, ?int $projectId): Cases
    {
        $case = Cases::createByIncomingSms($departmentId, $clientId, $projectId);
        $this->casesRepository->save($case);
        return $case;
    }

    /**
     * @param CasesCreateByWebForm $form
     * @param int $creatorId
     * @return Cases
     * @throws \Throwable
     */
    public function createByWeb(CasesCreateByWebForm $form, int $creatorId): Cases
    {
        $case = $this->transaction->wrap(function () use ($form, $creatorId) {
            $clientForm = ClientCreateForm::createWidthDefaultName();
            $clientForm->projectId = $form->projectId;
            $clientForm->typeCreate = Client::TYPE_CREATE_CASE;

            $client = $this->clientManageService->getOrCreate(
                [new PhoneCreateForm(['phone' => $form->clientPhone])],
                [new EmailCreateForm(['email' => $form->clientEmail])],
                $clientForm
            );

            $case = Cases::createByWeb(
                $form->projectId,
                $form->categoryId,
                $client->id,
                $form->depId,
                $form->subject,
                $form->description,
                $creatorId,
                $form->sourceTypeId,
                $form->orderUid,
                'Created by web'
            );
            $this->casesRepository->save($case);

            return $case;
        });

        return $case;
    }

    /**
     * @param PhoneCreateForm[] $clientPhones
     * @param int $callId
     * @param int $projectId
     * @param int|null $depId
     * @return Cases
     * @throws \Exception
     */
    public function createByCall(array $clientPhones, int $callId, int $projectId, ?int $depId): Cases
    {
        $case = $this->transaction->wrap(function () use ($clientPhones, $callId, $projectId, $depId) {
            $clientForm = ClientCreateForm::createWidthDefaultName();
            $clientForm->projectId = $projectId;
            $clientForm->typeCreate = Client::TYPE_CREATE_CASE;
            $client = $this->clientManageService->getOrCreateByPhones($clientPhones, $clientForm);
            $case = Cases::createByCall(
                $client->id,
                $callId,
                $projectId,
                $depId
            );
            $this->casesRepository->save($case);

            return $case;
        });

        return $case;
    }

    public function getOrCreateByCall(
        array $clientPhones,
        int $callId,
        int $callTo,
        int $projectId,
        int $depId,
        bool $createCaseOnIncoming,
        int $trashActiveDaysLimit
    ): ?Cases {
        $case = $this->transaction->wrap(function () use ($clientPhones, $callId, $callTo, $projectId, $depId, $createCaseOnIncoming, $trashActiveDaysLimit) {
            $clientForm = ClientCreateForm::createWidthDefaultName();
            $clientForm->projectId = $projectId;
            $clientForm->typeCreate = Client::TYPE_CREATE_CALL;

            if ($createCaseOnIncoming) {
                $client = $this->clientManageService->getOrCreateByPhones($clientPhones, $clientForm);
            } else {
                $client = $this->clientManageService->getExistingOrCreateEmptyObj($clientPhones, $clientForm);
            }

            if ((!$case = Cases::find()->findLastActiveClientCase($client->id, $projectId, $trashActiveDaysLimit)->byDepartment($depId)->one()) && $createCaseOnIncoming) {
                //\Yii::info('Not found case:  ' . VarDumper::dumpAsString(['ClientId' => $client->id, 'projectId' => $projectId, 'depId' => $depId]), 'info\getByClientProjectDepartment');
                $case = Cases::createByCall(
                    $client->id,
                    $callId,
                    $projectId,
                    $depId
                );
                $this->casesRepository->save($case);

                $departmentPhone = DepartmentPhoneProject::find()->byPhone($callTo, false)->enabled()->limit(1)->one();
                if ($departmentPhone) {
                    $departmentPhoneProjectParamsService = new DepartmentPhoneProjectParamsService($departmentPhone);
                    $departmentPhoneProjectParamsService->processExperiments(ExperimentTarget::EXT_TYPE_CASE, $case->cs_id);
                }
            } else {
                //\Yii::info('Find case: ' . $case->cs_id . ' - ' . VarDumper::dumpAsString(['ClientId' => $client->id, 'projectId' => $projectId, 'depId' => $depId]), 'info\getByClientProjectDepartment');
            }
            return $case;
        });

        return $case;
    }

    public function createByChat(CasesCreateByChatForm $form, ClientChat $chat, int $creatorId): Cases
    {
        $case = $this->transaction->wrap(function () use ($form, $chat, $creatorId) {
            if (!$client = $chat->cchClient) {
                throw new \DomainException('Client Chat not assigned with Client');
            }

            $case = Cases::createByWeb(
                $form->projectId,
                $form->categoryId,
                $client->id,
                $form->depId,
                $form->subject,
                $form->description,
                $creatorId,
                $form->sourceTypeId,
                $form->orderUid,
                'Created by Chat'
            );
            $this->casesRepository->save($case);

            $clientChatCase = ClientChatCase::create($chat->cch_id, $case->cs_id, new \DateTimeImmutable('now'));
            $this->chatCaseRepository->save($clientChatCase);

            return $case;
        });

        return $case;
    }

    public function createByCancelFailedOrder(Order $order): void
    {
        if (
            SettingHelper::isCreateCaseOnOrderCancelEnabled()
            &&
            $caseCategory = CaseCategory::find()->byKey(SettingHelper::getCaseCategoryKeyOnOrderCancel())->one()
        ) {
            try {
                $orderData = OrderData::findOne(['od_order_id' => $order->or_id]);

                $orderContact = OrderContact::find()->byOrderId($order->or_id)->one();
                if (!$orderContact) {
                    throw new \DomainException('Cannot create client, order contact not found');
                }

                if (!$client = $orderContact->client) {
                    $client = $this->clientManageService->createBasedOnOrderContact($orderContact, $order->or_project_id);
                }

                $case = Cases::createByApi(
                    $client->id,
                    $order->or_project_id,
                    $caseCategory->cc_dep_id,
                    $orderData->od_display_uid ?? null,
                    null,
                    null,
                    $caseCategory->cc_id
                );
                $this->casesRepository->save($case);

                $caseOrder = CaseOrder::create($case->cs_id, $order->or_id);
                $caseOrder->detachBehavior('user');
                if (!$caseOrder->save()) {
                    throw new \RuntimeException($caseOrder->getErrorSummary(true)[0]);
                }
            } catch (\Throwable $e) {
                \Yii::error(VarDumper::dumpAsString(AppHelper::throwableLog($e, true)), 'CasesCreateService:createByCancelFailedOrder:Throwable');
            }
        }
    }

    public function createRefund(string $bookingId, int $projectId, string $categoryKey): Cases
    {
        if (!$caseCategory = CaseCategory::findOne(['cc_key' => $categoryKey])) {
            throw new NotFoundException('CaseCategory (' . $categoryKey . ') not found');
        }

        $case = Cases::createByApiVoluntaryRefund(
            $caseCategory->cc_dep_id,
            $caseCategory->cc_id,
            $bookingId,
            $projectId,
            true
        );
        $this->casesRepository->save($case);

        $case->addEventLog(
            CaseEventLog::CASE_CREATED,
            'Voluntary Refund Create, BookingID: ' . $bookingId,
            ['case_gid' => $case->cs_gid, 'fr_booking_id' => $bookingId]
        );

        return $case;
    }
}
