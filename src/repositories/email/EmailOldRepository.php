<?php

namespace src\repositories\email;

use src\repositories\NotFoundException;
use common\models\Email;
use src\dispatchers\EventDispatcher;
use yii\db\Expression;
use common\models\EmailTemplateType;
use src\entities\email\helpers\EmailType;
use yii\db\ActiveQuery;

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

    public function getTodayCount($cache = 0)
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
        $lastInbox = Email::find()->select(['e_inbox_email_id'])->orderByLastInbox()->limit(1)->one();
        return $lastInbox['e_inbox_email_id'] ?? null;
    }
}
