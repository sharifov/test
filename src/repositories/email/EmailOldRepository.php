<?php

namespace src\repositories\email;

use src\repositories\NotFoundException;
use common\models\Email;
use src\dispatchers\EventDispatcher;
use yii\db\Expression;
use common\models\EmailTemplateType;
use src\entities\email\helpers\EmailType;
use yii\db\ActiveQuery;
use src\entities\email\helpers\EmailStatus;
use common\models\Lead;
use yii\db\Query;

class EmailOldRepository implements EmailRepositoryInterface
{
    private $eventDispatcher;

    public function __construct(EventDispatcher $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    public function find(int $id): Email
    {
        if ($email = Email::findOne($id)) {
            return $email;
        }
        throw new NotFoundException('Email not found. ID: ' . $id);
    }

    public function save($email): int
    {
        if (!$email->save()) {
            throw new \RuntimeException('Email save failed: ' . $email->getErrorSummary(true)[0]);
        }
        $this->eventDispatcher->dispatchAll($email->releaseEvents());
        return $email->e_id;
    }

    public function changeStatus($email, int $statusId): void
    {
        $attributes = ['e_status_id' => $statusId];
        if (EmailStatus::isDone($statusId)) {
            $attributes['e_status_done_dt'] = date('Y-m-d H:i:s');
        }
        $email->e_status_id = $statusId;
        $this->save($email);
    }

    public function saveInboxId($email, int $inboxId): void
    {
        $email->updateAttributes([
            'e_inbox_email_id' => $inboxId
        ]);
    }

    public function read($email): void
    {
        if ($email->e_is_new === true) {
            $email->updateAttributes([
                'e_is_new' => false,
                'e_read_dt' => date('Y-m-d H:i:s')
            ]);
        }
    }

    public function delete($email): int
    {
        $id = $email->e_id;
        if ($email->delete() === false) {
            throw new \RuntimeException('Removing error');
        }
        $this->eventDispatcher->dispatchAll($email->releaseEvents());
        return $id;
    }

    public function deleteByIds($id): array
    {
        $removedIds = [];
        foreach (Email::findAll(['e_id' => $id]) as $model) {
            $removedIds[] = $this->delete($model);
        }
        return $removedIds;
    }

    /**
     * @param array $mailList
     * @return int
     */
    public function getUnreadCount(array $mailList): int
    {
        return Email::find()->withContact($mailList)->notDeleted()->unread()->count();
    }

    /**
     * @param array $mailList
     * @return int
     */
    public function getInboxTodayCount(array $mailList): int
    {
        return Email::find()->withContact($mailList)->notDeleted()->inbox()->createdToday()->count();
    }

    /**
     * @param array $mailList
     * @return int
     */
    public function getOutboxTodayCount(array $mailList): int
    {
        return Email::find()->withContact($mailList)->notDeleted()->outbox()->createdToday()->count();
    }

    /**
     * @param array $mailList
     * @return int
     */
    public function getDraftCount(array $mailList): int
    {
        return Email::find()->withContact($mailList)->notDeleted()->draft()->count();
    }

    /**
     * @param array $mailList
     * @return int
     */
    public function getTrashCount(array $mailList): int
    {
        return Email::find()->withContact($mailList)->deleted()->count();
    }

    public function getTodayCount($cache = 0): int
    {
        return Email::find()->createdToday()->cache($cache)->count();
    }

    public function getEmailCountForLead(int $leadId, int $type = 0): int
    {
        $query = Email::find()->lead($leadId)->notDeleted();
        if($type > 0) {
            $query = $query->byType($type);
        }
        return $query->count();
    }

    public function getEmailCountByLead(int $leadId, $cache = 0): int
    {
        return Email::find()->lead($leadId)->cache($cache)->count();
    }

    public function getEmailCountByCase(int $caseId, $cache = 0): int
    {
        return Email::find()->case($caseId)->cache($cache)->count();
    }

    public function getCommunicationLogQueryForLead(int $leadId)
    {
        return Email::find()
            ->lead($leadId)
            ->select(['e_id AS id', new Expression('"email" AS type'), 'e_lead_id AS lead_id', 'e_created_dt AS created_dt']);
    }

    public function getCommunicationLogQueryForCase(int $caseId)
    {
        $emailTemplate = EmailTemplateType::find()
            ->select('etp_id')
            ->where(['etp_key' => 'feedback_appr_supp'])
            ->asArray()
            ->limit(1)
            ->one();

        $condition = $emailTemplate
            ? ['<>', 'e_template_type_id', $emailTemplate['etp_id']]
            : ['IS NOT', 'e_template_type_id', null];

        return Email::find()
            ->case($caseId)
            ->select(['e_id AS id', new Expression('"email" AS type'), 'e_case_id AS case_id', 'e_created_dt AS created_dt'])
            ->andWhere(['OR',
                ['IS NOT', 'e_created_user_id', null],
                ['AND',
                    ['IS', 'e_created_user_id', null],
                    ['e_type_id' => EmailType::INBOX],
                    ['IS', 'e_template_type_id', null]
                ],
                ['AND',
                    ['IS', 'e_created_user_id', null],
                    ['e_type_id' => EmailType::OUTBOX],
                    $condition
                ]
            ]);
    }

    public function findReceived(string $messageId, string $emailTo): ActiveQuery
    {
        return Email::find()->byMessageId($messageId)->byEmailToList([$emailTo]);
    }

    public function getLastInboxId(): ?int
    {
        $lastInbox = Email::find()->select(['e_inbox_email_id'])->orderByLastInbox()->limit(1)->scalar();
        return $lastInbox !== false ? $lastInbox : null;
    }

    public function getModelQuery(): ActiveQuery
    {
        return Email::find();
    }

    public function getTableName(): string
    {
        return Email::tableName();
    }

    public function getRawSqlCountGroupedByLead(): string
    {
        return Email::find()
            ->select([
                'e_lead_id',
                new Expression('COUNT(e_lead_id) AS cnt')
            ])
            ->groupBy(['e_lead_id'])
            ->createCommand()
            ->rawSql;
    }

    public function getRawSqlCountGroupedByCase(): string
    {
        return Email::find()
            ->select([
                'e_case_id',
                new Expression('COUNT(e_case_id) AS cnt')
            ])
            ->groupBy(['e_case_id'])
            ->createCommand()
            ->rawSql;
    }

    public function getQueryLastEmailByCase(int $caseId, int $type): ActiveQuery
    {
        $direction = EmailType::isInbox($type) ? 'In' : 'Out';
        return Email::find()
            ->select([
                new Expression('"email" AS type'),
                new Expression('"' . $direction . '" AS direction'),
                'e_case_id AS case_id',
                'MAX(e_created_dt) AS created_dt'
            ])
            ->case($caseId)
            ->byType($type);
    }

    public function getSubQueryLeadEmailOffer(): ActiveQuery
    {
        return Email::find()
            ->select(['count(*)'])
            ->byTemplateTypeId(1)
            ->andWhere(Email::tableName() . '.e_lead_id = ' . Lead::tableName() . '.id');
    }

    public function getCasesByEmailsToAndCreated($emailsTo, string $createdDate): ActiveQuery
    {
        return Email::find()
            ->select(['e_case_id'])
            ->andWhere('e_case_id IS NOT NULL')
            ->byEmailToList($emailsTo)
            ->created($createdDate)
            ->groupBy(['e_case_id']);
    }

    public function getCasesCreatorByEmailsToAndCreated($emailsTo, string $createdDate): ActiveQuery
    {
        return Email::find()
            ->select(['e_case_id', 'e_created_user_id'])
            ->andWhere('e_case_id IS NOT NULL')
            ->byEmailToList($emailsTo)
            ->created($createdDate)
            ->groupBy(['e_case_id', 'e_created_user_id']);
    }

    public function getStatsData(string $startDate, string $endDate, int $type)
    {
        return Email::find()
            ->select(['e_status_id', 'e_created_dt'])
            ->byStatus([EmailStatus::DONE, EmailStatus::ERROR])
            ->createdBetween($startDate, $endDate)
            ->byType($type, true)
            ->all();
    }
}
