<?php

namespace src\repositories\email;

use common\models\Client;
use common\models\DepartmentEmailProject;
use common\models\Lead;
use common\models\UserProjectParams;
use src\dispatchers\EventDispatcher;
use src\entities\cases\Cases;
use src\repositories\NotFoundException;
use src\entities\email\Email;
use yii\db\Expression;
use common\models\EmailTemplateType;
use src\entities\email\helpers\EmailType;
use src\entities\email\EmailParams;
use yii\db\ActiveQuery;
use src\entities\email\helpers\EmailStatus;

class EmailRepository implements EmailRepositoryInterface
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

    public function read($email): void
    {
        if ($email->emailLog && $email->isNew()) {
            $email->saveEmailLog([
                'el_is_new' => false,
                'el_read_dt' => date('Y-m-d H:i:s')
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

    public function saveInboxId($email, int $inboxId): void
    {
        $email->saveEmailLog([
            'el_inbox_email_id' => $inboxId
        ]);
    }

    /**
     *
     * @param Email $email
     * @param array $leadsIds
     * @return array
     */
    public function linkLeads($email, array $leadsIds): array
    {
        $linked = [];
        foreach ($leadsIds as $id) {
            if ($lead = Lead::findOne($id)) {
                $email->link('leads', $lead);
                $linked[] = $id;
            }
        }
        return $linked;
    }

    /**
     *
     * @param Email $email
     * @param array $casesIds
     * @return array
     */
    public function linkCases(Email $email, array $casesIds): array
    {
        $linked = [];
        foreach ($casesIds as $id) {
            if ($case = Cases::findOne($id)) {
                $email->link('cases', $case);
                $linked[] = $id;
            }
        }
        return $linked;
    }

    /**
     *
     * @param Email $email
     * @param array $clientsIds
     * @return array
     */
    public function linkClients(Email $email, array $clientsIds): array
    {
        $linked = [];
        foreach ($clientsIds as $id) {
            if ($client = Client::findOne($id)) {
                $email->link('clients', $client);
                $linked[] = $id;
            }
        }
        return $linked;
    }

    /**
     *
     * @param Email $email
     * @param int $replyId
     * @return bool
     */
    public function linkReply(Email $email, int $replyId): bool
    {
        $reply = Email::findOne($replyId);
        if ($reply) {
            $email->link('reply', $reply);
            return true;
        }
        return false;
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
        $connection = \Yii::$app->getDb();
        $command = $connection->createCommand(
            "SELECT COUNT(*) as cnt
            FROM email_lead el
            LEFT JOIN " . Email::tableName() . " AS e ON e.e_id = el.el_email_id
            WHERE el_lead_id = $leadId AND e_is_deleted = 0" .
            (($type != 0) ? " AND e_type_id = " . $type : "")
        );
        return $command->queryScalar();
    }

    public function getEmailCountByLead(int $leadId, $cache = 0): int
    {
        $connection = \Yii::$app->getDb();
        $command = $connection->createCommand(
            "SELECT COUNT(*) as cnt
             FROM email_lead
             WHERE el_lead_id = $leadId"
        );
        return $command->cache($cache)->queryScalar();
    }

    public function getEmailCountByCase(int $caseId, $cache = 0): int
    {
        $connection = \Yii::$app->getDb();
        $command = $connection->createCommand(
            "SELECT COUNT(*) as cnt
            FROM email_case
            WHERE ec_case_id = $caseId"
        );
        return $command->cache($cache)->queryScalar();
    }

    public static function getProjectIdByDepOrUpp($emailTo): ?int
    {
        if ($dep = DepartmentEmailProject::find()->byEmail($emailTo)->one()) {
            return $dep->dep_project_id;
        } else if ($upp = UserProjectParams::find()->byEmail($emailTo)->one()) {
            return $upp->upp_project_id;
        }

        return null;
    }

    public function getCommunicationLogQueryForLead(int $leadId)
    {
        return Email::find()
            ->lead($leadId)
            ->select(['e_id AS id', new Expression('"email" AS type'), 'el_lead_id AS lead_id', 'e_created_dt AS created_dt']);
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
            ? ['<>', 'ep_template_type_id', $emailTemplate['etp_id']]
            : ['IS NOT', 'ep_template_type_id', null];

        return Email::find()
            ->case($caseId)
            ->select(['e_id AS id', new Expression('"email" AS type'), 'ec_case_id AS case_id', 'e_created_dt AS created_dt'])
            ->leftJoin(EmailParams::tableName(), 'ep_email_id = e_id')
            ->andWhere(['OR',
                ['IS NOT', 'e_created_user_id', null],
                ['AND',
                    ['IS', 'e_created_user_id', null],
                    ['e_type_id' => EmailType::INBOX],
                    ['IS', 'ep_template_type_id', null]
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
        $lastInbox = Email::find()->addSelect(['el_inbox_email_id'])->orderByLastInbox()->limit(1)->scalar();
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

    public function findByCommunicationId(int $communicationId): Email
    {
        if ($email = Email::find()->byCommunicationId($communicationId)->limit(1)->one()) {
            return $email;
        }
        throw new NotFoundException('Email not found. Communication ID: ' . $communicationId);
    }

    public function getRawSqlCountGroupedByLead(): string
    {
        return \Yii::$app->getDb()->createCommand(
            "SELECT el_lead_id AS e_lead_id, COUNT(*) as cnt
                FROM email_lead el
                GROUP BY el_lead_id"
        )
            ->rawSql;
    }

    public function getRawSqlCountGroupedByCase(): string
    {
        return \Yii::$app->getDb()->createCommand(
            "SELECT ec_case_id AS e_case_id, COUNT(*) as cnt
                FROM email_case ec
                GROUP BY ec_case_id"
        )
            ->rawSql;
    }

    public function getQueryLastEmailByCase(int $caseId, int $type): ActiveQuery
    {
        $direction = EmailType::isInbox($type) ? 'In' : 'Out';
        return Email::find()
            ->select([
                new Expression('"email" AS type'),
                new Expression('"' . $direction . '" AS direction'),
                'ec_case_id AS case_id',
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
            ->leftJoin('email_lead', 'email_lead.el_email_id = ' . Email::tableName() . '.e_id')
            ->andWhere('email_lead.el_lead_id = ' . Lead::tableName() . '.id');
    }

    public function getCasesByEmailsToAndCreated($emailsTo, string $createdDate): ActiveQuery
    {
        return Email::find()
            ->leftJoin('email_case', 'email_case.ec_email_id = e_id')
            ->select(['ec_case_id as e_case_id'])
            ->byEmailToList($emailsTo)
            ->created($createdDate)
            ->groupBy(['e_case_id']);
    }

    public function getCasesCreatorByEmailsToAndCreated($emailsTo, string $createdDate): ActiveQuery
    {
        return Email::find()
            ->leftJoin('email_case', 'email_case.ec_email_id = e_id')
            ->select(['ec_case_id as e_case_id', 'e_created_user_id'])
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
