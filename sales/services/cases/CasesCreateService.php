<?php

namespace sales\services\cases;

use common\models\Client;
use sales\entities\cases\Cases;
use sales\forms\cases\CasesCreateByChatForm;
use sales\forms\cases\CasesCreateByWebForm;
use sales\forms\lead\EmailCreateForm;
use sales\forms\lead\PhoneCreateForm;
use sales\model\clientChat\entity\ClientChat;
use sales\model\clientChatCase\entity\ClientChatCase;
use sales\model\clientChatCase\entity\ClientChatCaseRepository;
use sales\repositories\cases\CasesRepository;
use sales\services\client\ClientCreateForm;
use sales\services\client\ClientManageService;
use sales\services\TransactionManager;

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
    )
    {
        $this->casesRepository = $casesRepository;
        $this->clientManageService = $clientManageService;
        $this->transaction = $transaction;
        $this->casesSaleService = $casesSaleService;
        $this->chatCaseRepository = $chatCaseRepository;
    }

    /**
     * @param int $clientId
     * @param int|null $projectId
     * @return Cases
     */
    public function createSupportByIncomingEmail(int $clientId, ?int $projectId): Cases
    {
        $case = Cases::createSupportByIncomingEmail($clientId, $projectId);
        $this->casesRepository->save($case);
        return $case;
    }

    /**
     * @param int $clientId
     * @param int|null $projectId
     * @return Cases
     */
    public function createExchangeByIncomingEmail(int $clientId, ?int $projectId): Cases
    {
        $case = Cases::createExchangeByIncomingEmail($clientId, $projectId);
        $this->casesRepository->save($case);
        return $case;
    }

    /**
     * @param int $clientId
     * @param int|null $projectId
     * @return Cases
     */
    public function createExchangeByIncomingSms(int $clientId, ?int $projectId): Cases
    {
        $case = Cases::createExchangeByIncomingSms($clientId, $projectId);
        $this->casesRepository->save($case);
        return $case;
    }

    /**
     * @param int $clientId
     * @param int|null $projectId
     * @return Cases
     */
    public function createSupportByIncomingSms(int $clientId, ?int $projectId): Cases
    {
        $case = Cases::createSupportByIncomingSms($clientId, $projectId);
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

    public function getOrCreateByCall(array $clientPhones, int $callId, int $projectId, int $depId, bool $createCaseOnIncoming = true): ?Cases
    {

        $case = $this->transaction->wrap(function () use ($clientPhones, $callId, $projectId, $depId, $createCaseOnIncoming) {

            $clientForm = ClientCreateForm::createWidthDefaultName();
            $clientForm->projectId = $projectId;
            $clientForm->typeCreate = Client::TYPE_CREATE_CASE;

        	if ($createCaseOnIncoming) {
				$client = $this->clientManageService->getOrCreateByPhones($clientPhones, $clientForm);
			} else {
        		$client = $this->clientManageService->getExistingOrCreateEmptyObj($clientPhones, $clientForm);
			}

            if ((!$case = Cases::find()->findLastActiveCaseByClient($client->id, $projectId)->byDepartment($depId)->one()) && $createCaseOnIncoming) {
                //\Yii::info('Not found case:  ' . VarDumper::dumpAsString(['ClientId' => $client->id, 'projectId' => $projectId, 'depId' => $depId]), 'info\getByClientProjectDepartment');
                $case = Cases::createByCall(
                    $client->id,
                    $callId,
                    $projectId,
                    $depId
                );
                $this->casesRepository->save($case);

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
}
