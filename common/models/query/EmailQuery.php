<?php

namespace common\models\query;

use yii\db\ActiveQuery;
use common\models\Email;
use src\entities\email\helpers\EmailType;
use yii\db\Expression;

/**
 * This is the ActiveQuery class for [[Email]].
 *
 * @see Email
 */
class EmailQuery extends ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return Email[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return Email|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }

    public function byEmailToList(array $emails): EmailQuery
    {
        return $this->andWhere(['IN', 'e_email_to', $emails]);
    }

    public function byDateSend(string $date): EmailQuery
    {
        return $this->andWhere(['date_format(e_created_dt, "%Y-%m-%d")' => $date]);
    }

    public function byMessageId(string $messageId)
    {
        return $this->andWhere(['e_message_id' => $messageId]);
    }

    public function byCommunicationId(int $communicationId)
    {
        return $this->andWhere(['e_communication_id' => $communicationId]);
    }

    public function byInboxId(int $inboxId)
    {
        return $this->andWhere(['e_inbox_email_id' => $inboxId]);
    }

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
     * @param int|array $status
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
        return $this->andWhere(['e_is_new' => true]);
    }
    public function lead(int $leadId)
    {
        return $this->andWhere(['e_lead_id' => $leadId]);
    }

    public function case(int $caseId)
    {
        return $this->andWhere(['e_case_id' => $caseId]);
    }

    public function orderByLastInbox()
    {
        return $this->andWhere(['>', 'e_inbox_email_id', 0])->orderBy(['e_inbox_email_id' => SORT_DESC]);
    }

    public function byTemplateTypeId(int $templateTypeId)
    {
        return $this->andWhere(['e_template_type_id' => $templateTypeId]);
    }

    public function withContact(array $mailList)
    {
        return $this->andWhere(['OR',
            ['e_email_to' => $mailList],
            ['e_email_from' => $mailList],
        ]);
    }

    public function notNormalized()
    {
        return $this
            ->leftJoin(\src\entities\email\Email::tableName() . ' AS en', 'en.e_id = email.e_id')
            ->where('en.e_id IS NULL')
            ->orderBy('email.e_id DESC');
    }
}
