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
}
