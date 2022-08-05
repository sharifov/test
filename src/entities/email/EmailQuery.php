<?php

namespace src\entities\email;

use yii\db\ActiveQuery;
use yii\helpers\ArrayHelper;
use src\entities\email\helpers\EmailType;
use yii\db\Expression;
use src\entities\email\helpers\EmailContactType;

class EmailQuery extends ActiveQuery
{
    public function notDeleted()
    {
        return $this->andWhere(['e_is_deleted' => false]);
    }

    public function deleted()
    {
        return $this->andWhere(['e_is_deleted' => true]);
    }

    /**
     *
     * @param int|arrat $status
     * @return EmailQuery
     */
    public function byStatus($status)
    {
        return $this->andWhere(['e_status_id' => $status]);
    }

    public function byType(int $type, $ignoreEmpty = false)
    {
        if ($ignoreEmpty && $type == 0) {
            return $this;
        }
        return $this->andWhere(['e_type_id' => $type]);
    }

    public function inbox()
    {
        return $this->byType(EmailType::INBOX);
    }

    public function outbox()
    {
        return $this->byType(EmailType::OUTBOX);
    }

    public function draft()
    {
        return $this->byType(EmailType::DRAFT);
    }

    public function createdToday()
    {
        return $this->andWhere(['DATE(e_created_dt)' => new Expression('DATE(NOW())')]);
    }

    public function created(string $date)
    {
        return $this->andWhere(['DATE(e_created_dt)' => $date]);
    }

    public function createdBetween(string $dateFrom, string $dateTo)
    {
        return $this->andWhere(['BETWEEN','e_created_dt', $dateFrom, $dateTo]);
    }

    public function createdBy(int $userId)
    {
        return $this->andWhere(['e_created_user_id' => $userId]);
    }

    public function unread()
    {
        return $this
            ->leftJoin(['el' => EmailLog::tableName()], 'el.el_email_id = e_id')
            ->andWhere(['el_is_new' => true]);
    }

    /**
     *
     * @param array $mailList
     * @return \src\entities\email\EmailQuery
     */
    public function withContact(array $mailList)
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
     * @return \src\entities\email\EmailQuery
     */
    public function withContactByType(array $mailList, int $type)
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

    public function lead(int $leadId)
    {
        return $this->leftJoin('email_lead', 'email_lead.el_email_id = e_id')->andWhere(['el_lead_id' => $leadId]);
    }

    public function case(int $caseId)
    {
        return $this->leftJoin('email_case', 'email_case.ec_email_id = e_id')->andWhere(['email_case.ec_case_id' => $caseId]);
    }

    public function byMessageId(string $messageId)
    {
        return $this->leftJoin(['el' => EmailLog::tableName()], 'el.el_email_id = e_id')->andWhere(['el.el_message_id' => $messageId]);
    }

    public function byCommunicationId(int $communicationId)
    {
        return $this->leftJoin(['el' => EmailLog::tableName()], 'el.el_email_id = e_id')->andWhere(['el.el_communication_id' => $communicationId]);
    }

    public function byInboxId(int $inboxId)
    {
        return $this->leftJoin(['el' => EmailLog::tableName()], 'el.el_email_id = e_id')->andWhere(['el.el_inbox_email_id' => $inboxId]);
    }

    public function byEmailToList(array $mailList)
    {
        return $this->withContactByType($mailList, EmailContactType::TO);
    }

    public function byEmailFromList(array $mailList)
    {
        return $this->withContactByType($mailList, EmailContactType::FROM);
    }

    public function orderByLastInbox()
    {
        return $this->leftJoin(['el' => EmailLog::tableName()], 'el.el_email_id = e_id')->andWhere(['>', 'el_inbox_email_id', 0])->orderBy(['el_inbox_email_id' => SORT_DESC]);
    }

    public function byTemplateTypeId(int $templateTypeId)
    {
        return $this->leftJoin(['ep' => EmailParams::tableName()], 'ep.ep_email_id = e_id')->andWhere(['ep_template_type_id' => $templateTypeId]);
    }
}
