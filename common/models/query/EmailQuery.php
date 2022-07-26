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

    public function notDeleted()
    {
        return $this->andWhere(['e_is_deleted' => false]);
    }

    public function deleted()
    {
        return $this->andWhere(['e_is_deleted' => true]);
    }

    public function byType(int $type)
    {
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
}
