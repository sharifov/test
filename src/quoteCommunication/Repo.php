<?php

namespace src\quoteCommunication;

use common\models\Email;
use common\models\QuoteCommunication;
use frontend\models\CommunicationForm;

/**
 * Class Repo
 * @package src\quoteCommunication
 */
class Repo
{
    /**
     * @param Email $email
     * @param int $quoteId
     * @return bool
     */
    public static function createForEmail(Email $email, int $quoteId): bool
    {
        $model = new QuoteCommunication([
            'qc_communication_type' => CommunicationForm::TYPE_EMAIL,
            'qc_communication_id' => $email->e_id,
            'qc_quote_id' => $quoteId
        ]);
        return $model->save();
    }

    /**
     * @param int $chatId
     * @param int $quoteId
     * @return bool
     */
    public static function createForChat(int $chatId, int $quoteId): bool
    {
        $model = new QuoteCommunication([
            'qc_communication_type' => CommunicationForm::TYPE_CHAT,
            'qc_communication_id' => $chatId,
            'qc_quote_id' => $quoteId
        ]);
        return $model->save();
    }

    /**
     * @param int $smsId
     * @param int $quoteId
     * @return bool
     */
    public static function createForSms(int $smsId, int $quoteId): bool
    {
        $model = new QuoteCommunication([
            'qc_communication_type' => CommunicationForm::TYPE_SMS,
            'qc_communication_id' => $smsId,
            'qc_quote_id' => $quoteId
        ]);
        return $model->save();
    }
}
