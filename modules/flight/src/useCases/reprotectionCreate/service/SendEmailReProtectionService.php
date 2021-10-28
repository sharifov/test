<?php

namespace modules\flight\src\useCases\reprotectionCreate\service;

use common\models\ClientEmail;
use modules\order\src\entities\order\Order;
use modules\product\src\entities\productQuote\ProductQuote;
use modules\product\src\entities\productQuoteChange\ProductQuoteChange;
use modules\product\src\entities\productQuoteChange\ProductQuoteChangeRepository;
use sales\entities\cases\CaseEventLog;
use sales\entities\cases\Cases;
use sales\exception\CheckRestrictionException;
use sales\helpers\ProjectHashGenerator;
use sales\services\cases\CasesCommunicationService;
use sales\services\email\SendEmailByCase;
use Yii;
use yii\helpers\ArrayHelper;
use yii\validators\EmailValidator;

/**
 * Class SendEmailReProtectionService
 *
 * @property CasesCommunicationService $casesCommunicationService
 * @property ProductQuoteChangeRepository $productQuoteChangeRepository
 *
 * @property int|null $sendResultStatus
 */
class SendEmailReProtectionService
{
    private ?int $sendResultStatus = null;

    private CasesCommunicationService $casesCommunicationService;
    private ProductQuoteChangeRepository $productQuoteChangeRepository;

    public function __construct(CasesCommunicationService $casesCommunicationService, ProductQuoteChangeRepository $productQuoteChangeRepository)
    {
        $this->casesCommunicationService = $casesCommunicationService;
        $this->productQuoteChangeRepository = $productQuoteChangeRepository;
    }

    public function processing(
        Cases $case,
        ?Order $order,
        ProductQuote $reProtectionQuote,
        ?ProductQuote $originProductQuote,
        ProductQuoteChange $productQuoteChange
    ): ?int {
        $clientEmail = self::detectEmail($case, $order);

        $emailData = $this->casesCommunicationService->getEmailDataWithoutAgentData($case);
        $emailData['reprotection_quote'] = $reProtectionQuote->serialize();
        if ($originProductQuote) {
            $emailData['original_quote'] = $originProductQuote->serialize();
            $bookingId = ArrayHelper::getValue($emailData, 'original_quote.data.flights.0.fqf_booking_id', '');
            $emailData['booking_hash_code'] = ProjectHashGenerator::getHashByProjectId($case->cs_project_id, $bookingId);
        }

        $this->sendResultStatus = (new SendEmailByCase($case->cs_id, $clientEmail, $emailData))->getResultStatus();
        if ($this->sendResultStatus === SendEmailByCase::RESULT_NOT_ENABLE) {
            throw new CheckRestrictionException('ClientEmail not send. EmailConfigs not enabled.');
        }
        if ($this->sendResultStatus !== SendEmailByCase::RESULT_SEND) {
            throw new CheckRestrictionException('ClientEmail not send');
        }

        if ($originProductQuote && isset($productQuoteChange)) {
            $productQuoteChange->statusToPending();
            $this->productQuoteChangeRepository->save($productQuoteChange);
        }
        $case->addEventLog(CaseEventLog::RE_PROTECTION_CREATE, 'Email sent successfully');

        return $this->sendResultStatus;
    }

    public static function detectEmail(Cases $case, ?Order $order, bool $findFromCaseClient = false): string
    {
        if ($order && $order->orderContacts) {
            $emailValidator = new EmailValidator();
            foreach ($order->orderContacts as $orderContact) {
                if (!empty($orderContact->oc_email) && $emailValidator->validate($orderContact->oc_email)) {
                    return $orderContact->oc_email;
                }
            }
        }
        if ($findFromCaseClient && $clientEmail = ClientEmail::getGeneralEmail($case->cs_client_id)) {
            return $clientEmail;
        }
        throw new CheckRestrictionException('ClientEmail not found');
    }
}
