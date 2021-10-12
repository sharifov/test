<?php

namespace modules\flight\src\useCases\reprotectionExchange\service;

use common\components\purifier\Purifier;
use common\models\CaseNote;
use common\models\ClientEmail;
use common\models\ClientPhone;
use common\models\Notifications;
use modules\flight\src\useCases\reprotectionExchange\form\ReProtectionExchangeForm;
use modules\product\src\entities\productQuoteChange\ProductQuoteChange;
use modules\product\src\entities\productQuoteChange\ProductQuoteChangeRepository;
use modules\product\src\entities\productQuoteChange\ProductQuoteChangeStatus;
use sales\entities\cases\CaseEventLog;
use sales\entities\cases\Cases;
use sales\forms\lead\EmailCreateForm;
use sales\forms\lead\PhoneCreateForm;
use sales\helpers\ErrorsToStringHelper;
use sales\repositories\cases\CasesRepository;
use sales\services\client\ClientManageService;
use Yii;

/**
 * Class ReProtectionExchangeService
 *
 * @property ProductQuoteChangeRepository $productQuoteChangeRepository
 * @property ClientManageService $clientManageService
 * @property CasesRepository $casesRepository
 */
class ReProtectionExchangeService
{
    private ProductQuoteChangeRepository $productQuoteChangeRepository;
    private ClientManageService $clientManageService;
    private CasesRepository $casesRepository;

    /**
     * @param ProductQuoteChangeRepository $productQuoteChangeRepository
     * @param ClientManageService $clientManageService
     * @param CasesRepository $casesRepository
     */
    public function __construct(
        ProductQuoteChangeRepository $productQuoteChangeRepository,
        ClientManageService $clientManageService,
        CasesRepository $casesRepository
    ) {
        $this->productQuoteChangeRepository = $productQuoteChangeRepository;
        $this->clientManageService = $clientManageService;
        $this->casesRepository = $casesRepository;
    }

    public function handle(ReProtectionExchangeForm $reProtectionExchangeForm): void
    {
        if (!$case = $reProtectionExchangeForm->getCase()) {
            throw new \DomainException('Case not found', 110);
        }
        if (!$productQuoteChange = $case->productQuoteChange) {
            throw new \DomainException('ProductQuoteChange not found', 111);
        }

        $case->addEventLog(CaseEventLog::RE_PROTECTION_EXCHANGE, 'Exchange request started processing');

        if (!$productQuoteChange->isDecisionPending()) {
            $message = 'ProductQuoteChange not in status ' .
                ProductQuoteChangeStatus::getName(ProductQuoteChangeStatus::DECISION_PENDING);

            $case->addEventLog(CaseEventLog::RE_PROTECTION_EXCHANGE, $message);
            throw new \DomainException($message, 101);
        }

        $productQuoteChange->statusToNew();
        $this->productQuoteChangeRepository->save($productQuoteChange);

        if ($reProtectionExchangeForm->isEmailValid()) {
            $this->clientManageService->addEmail(
                $case->client,
                new EmailCreateForm(['email' => $reProtectionExchangeForm->email, 'type' => ClientEmail::EMAIL_NOT_SET])
            );
        }
        if ($reProtectionExchangeForm->isPhoneValid()) {
            $this->clientManageService->addPhone(
                $case->client,
                new PhoneCreateForm(['phone' => $reProtectionExchangeForm->phone, 'type' => ClientPhone::PHONE_NOT_SET])
            );
        }

        if (!empty($reProtectionExchangeForm->flight_request)) {
            $caseNote = CaseNote::create($case->cs_id, $reProtectionExchangeForm->flight_request, null);
            $caseNote->detachBehavior('user');
            if ($caseNote->validate()) {
                $caseNote->save();
            } else {
                $message = [
                    'message' => ErrorsToStringHelper::extractFromModel($caseNote),
                    'data' => $caseNote->getAttributes(),
                ];
                Yii::warning($message, 'ReProtectionExchangeService:CaseNote:validate');
            }
        }

        if (!$case->isNeedAction()) {
            $case->onNeedAction();
        }
        if ($case->isAutomate()) {
            $case->offIsAutomate();
        }
        if (
            !$case->isPending()
            &&
            ($case->isStatusAutoProcessing() || $case->isSolved() || $case->isTrash() || $case->isFollowUp())
        ) {
            $case->pending(null, 'ReProtection quote change requested');
        }
        $this->casesRepository->save($case);

        if ($case->cs_user_id) {
            $linkToCase = Purifier::createCaseShortLink($case);
            Notifications::createAndPublish(
                $case->cs_user_id,
                'ReProtection quote exchange requested by client',
                'Case (' . $linkToCase . ') - ReProtection quote exchange requested by client',
                Notifications::TYPE_INFO,
                true
            );
        }
        $case->addEventLog(CaseEventLog::RE_PROTECTION_EXCHANGE, 'Exchange request processed successfully');
    }

    public static function getCaseByBookingId(string $bookingId): ?Cases
    {
        return Cases::find()
            ->select(Cases::tableName() . '.*')
            ->innerJoin([ProductQuoteChange::tableName(), 'cs_id = pqc_case_id'])
            ->where(['cs_order_uid' => $bookingId])
            ->orderBy(['cs_id' => SORT_DESC])
            ->one();
    }
}
