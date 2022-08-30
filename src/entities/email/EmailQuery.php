<?php

namespace src\entities\email;

use yii\db\ActiveQuery;
use yii\helpers\ArrayHelper;
use src\entities\email\helpers\EmailType;
use yii\db\Expression;
use src\entities\email\helpers\EmailContactType;

class EmailQuery extends ActiveQuery
{
    public function notDeleted(): EmailQuery
    {
        return $this->andWhere(['e_is_deleted' => false]);
    }

    public function deleted(): EmailQuery
    {
        return $this->andWhere(['e_is_deleted' => true]);
    }

    /**
     *
     * @param int|array $status
     * @return EmailQuery
     */
    public function byStatus($status): EmailQuery
    {
        return $this->andWhere(['e_status_id' => $status]);
    }

    public function byType(int $type, $ignoreEmpty = false): EmailQuery
    {
        if ($ignoreEmpty && $type == 0) {
            return $this;
        }
        return $this->andWhere(['e_type_id' => $type]);
    }

    public function inbox(): EmailQuery
    {
        return $this->byType(EmailType::INBOX);
    }

    public function outbox(): EmailQuery
    {
        return $this->byType(EmailType::OUTBOX);
    }

    public function draft(): EmailQuery
    {
        return $this->byType(EmailType::DRAFT);
    }

    public function createdToday(): EmailQuery
    {
        return $this->andWhere(['DATE(e_created_dt)' => new Expression('DATE(NOW())')]);
    }

    public function created(string $date): EmailQuery
    {
        return $this->andWhere(['DATE(e_created_dt)' => $date]);
    }

    public function createdBetween(string $dateFrom, string $dateTo): EmailQuery
    {
        return $this->andWhere(['BETWEEN','e_created_dt', $dateFrom, $dateTo]);
    }

    public function createdBy(int $userId): EmailQuery
    {
        return $this->andWhere(['e_created_user_id' => $userId]);
    }

    public function unread(): EmailQuery
    {
        return $this
            ->leftJoin(['el' => EmailLog::tableName()], 'el.el_email_id = e_id')
            ->andWhere(['el_is_new' => true]);
    }

    /**
     *
     * @param array $mailList
     * @return EmailQuery
     */
    public function withContact(array $mailList): EmailQuery
    {
        $addresses = EmailAddress::find()
                ->select('ea_id')
                ->where(['ea_email' => $mailList])
                ->asArray()
                ->all();
        $address_ids = ArrayHelper::getColumn($addresses, 'ea_id');

        return $this
            ->join('LEFT JOIN', ['ec' => EmailContact::tableName()], 'ec.ec_email_id = e_id')
            ->andWhere(['ec.ec_address_id' => $address_ids]);
    }

    /**
     *
     * @param array $mailList
     * @param int $type
     * @return EmailQuery
     */
    public function withContactByType(array $mailList, int $type): EmailQuery
    {
        $addresses = EmailAddress::find()
        ->select('ea_id')
        ->where(['ea_email' => $mailList])
        ->asArray()
        ->all();
        $address_ids = ArrayHelper::getColumn($addresses, 'ea_id');

        $synonim = 'ec' . $type;
        return $this
            ->join('LEFT JOIN', [$synonim => EmailContact::tableName()], $synonim . '.ec_email_id = e_id')
            ->andWhere([$synonim . '.ec_address_id' => $address_ids, $synonim . '.ec_type_id' => $type]);
    }

    public function lead(int $leadId): EmailQuery
    {
        return $this->leftJoin('email_lead', 'email_lead.el_email_id = e_id')->andWhere(['el_lead_id' => $leadId]);
    }

    public function case(int $caseId): EmailQuery
    {
        return $this->leftJoin('email_case', 'email_case.ec_email_id = e_id')->andWhere(['email_case.ec_case_id' => $caseId]);
    }

    public function client(int $clientId): EmailQuery
    {
        return $this->leftJoin('email_client', 'email_client.ecl_email_id = e_id')->andWhere(['email_client.ecl_client_id' => $clientId]);
    }

    public function byMessageId(string $messageId): EmailQuery
    {
        return $this->leftJoin(['el' => EmailLog::tableName()], 'el.el_email_id = e_id')->andWhere(['el.el_message_id' => $messageId]);
    }

    public function byCommunicationId(int $communicationId): EmailQuery
    {
        return $this->leftJoin(['el' => EmailLog::tableName()], 'el.el_email_id = e_id')->andWhere(['el.el_communication_id' => $communicationId]);
    }

    public function byInboxId(int $inboxId): EmailQuery
    {
        return $this->leftJoin(['el' => EmailLog::tableName()], 'el.el_email_id = e_id')->andWhere(['el.el_inbox_email_id' => $inboxId]);
    }

    public function byEmailToList(array $mailList): EmailQuery
    {
        return $this->withContactByType($mailList, EmailContactType::TO);
    }

    public function byEmailFromList(array $mailList): EmailQuery
    {
        return $this->withContactByType($mailList, EmailContactType::FROM);
    }

    public function orderByLastInbox(): EmailQuery
    {
        return $this->leftJoin(['el' => EmailLog::tableName()], 'el.el_email_id = e_id')->andWhere(['>', 'el_inbox_email_id', 0])->orderBy(['el_inbox_email_id' => SORT_DESC]);
    }

    public function byTemplateTypeId(int $templateTypeId): EmailQuery
    {
        return $this->leftJoin(['ep' => EmailParams::tableName()], 'ep.ep_email_id = e_id')->andWhere(['ep_template_type_id' => $templateTypeId]);
    }
}
