<?php

namespace modules\flight\src\useCases\voluntaryExchange\service;

use common\models\ClientEmail;
use modules\order\src\entities\order\Order;
use modules\product\src\entities\productQuote\ProductQuote;
use modules\product\src\entities\productQuoteChange\ProductQuoteChange;
use modules\product\src\entities\productQuoteChange\ProductQuoteChangeRepository;
use src\entities\cases\CaseEventLog;
use src\entities\cases\Cases;
use src\exception\CheckRestrictionException;
use src\helpers\ProjectHashGenerator;
use src\services\cases\CasesCommunicationService;
use src\services\email\SendEmailByCase;
use Yii;
use yii\helpers\ArrayHelper;
use yii\validators\EmailValidator;

/**
 * Class SendEmailReProtectionService
 *
 *
 * @property int|null $sendResultStatus
 */
class SendEmailVoluntaryExchangeService
{
    private ?int $sendResultStatus = null;

    /**
     * @param Cases $case
     * @param Order|null $order
     * @param ProductQuote $voluntaryExchangeQuote
     * @param ProductQuote $originProductQuote
     * @param string $bookingId
     * @param VoluntaryExchangeObjectCollection $objectCollection
     */
    public function __construct(
        Cases $case,
        ?Order $order,
        ProductQuote $voluntaryExchangeQuote,
        ProductQuote $originProductQuote,
        string $bookingId,
        VoluntaryExchangeObjectCollection $objectCollection
    ) {
        $clientEmail = self::detectEmail($case, $order);

        $emailData = $objectCollection->getCasesCommunicationService()->getEmailDataWithoutAgentData($case);
        $emailData['voluntary_exchange_quote'] = $voluntaryExchangeQuote->serialize();
        if ($originProductQuote) {
            $emailData['original_quote'] = $originProductQuote->serialize();
            $emailData['booking_hash_code'] = ProjectHashGenerator::getHashByProjectId($case->cs_project_id, $bookingId);
        }

        $this->sendResultStatus = (new SendEmailByCase($case->cs_id, $clientEmail, $emailData))->getResultStatus();
        if ($this->sendResultStatus === SendEmailByCase::RESULT_NOT_ENABLE) {
            throw new CheckRestrictionException('ClientEmail not send. EmailConfigs not enabled.');
        }
        if ($this->sendResultStatus !== SendEmailByCase::RESULT_SEND) {
            throw new CheckRestrictionException('ClientEmail not send');
        }

        $case->addEventLog(CaseEventLog::VOLUNTARY_EXCHANGE_CREATE, 'Email sent successfully');
    }

    private static function detectEmail(Cases $case, ?Order $order, bool $findFromCaseClient = false): string
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

    public function getSendResultStatus(): ?int
    {
        return $this->sendResultStatus;
    }
}
