<?php

namespace src\helpers\communication;

use common\models\Call;
use common\models\Email;
use common\models\Lead;
use common\models\Sms;
use src\entities\cases\Cases;
use src\entities\cases\CasesStatus;
use src\model\callLog\entity\callLog\CallLog;
use src\model\callLog\entity\callLog\CallLogType;
use src\model\callLog\entity\callLogCase\CallLogCase;
use src\model\callLog\entity\callLogLead\CallLogLead;
use src\model\clientChat\entity\ClientChat;
use src\model\clientChatCase\entity\ClientChatCase;
use src\model\clientChatLead\entity\ClientChatLead;
use Yii;
use yii\data\ActiveDataProvider;
use yii\db\Expression;
use yii\db\Query;
use yii\helpers\ArrayHelper;
use modules\featureFlag\FFlag;
use src\entities\email\EmailRepository;

/**
 * Class StatisticsHelper
 * @property int $id
 */
class StatisticsHelper
{
    public int $emailCount = 0;
    public int $smsCount = 0;
    public int $callCount = 0;
    public int $clientChatCount = 0;

    public const TYPE_LEAD = 'lead';
    public const TYPE_CASE = 'case';
    public const HINT_EMAILS = 'Emails';
    public const HINT_SMS = 'Sms';
    public const HINT_CALLS = 'Calls';
    public const HINT_CHATS = 'Chats';

    protected $id;
    protected string $type = 'lead';
    protected int $cacheDuration = -1; // noCache mode = "-1"
    protected const ALLOWED_TYPES = [self::TYPE_LEAD, self::TYPE_CASE];

    /**
     * @param int $id
     * @param string $type
     */
    public function __construct(int $id, string $type)
    {
        if (!ArrayHelper::isIn($type, self::ALLOWED_TYPES)) {
            throw new \DomainException('Type (' . $type . ') is not allowed.');
        }
        $this->id = $id;
        $this->type = $type;
    }

    /**
     * @return $this
     */
    public function setEmailCount(): StatisticsHelper
    {
        if (Yii::$app->featureFlag->isEnable(FFlag::FF_KEY_EMAIL_NORMALIZED_FORM_ENABLE)) {
            if ($this->type === self::TYPE_LEAD) {
                $this->emailCount = EmailRepository::getEmailCountByLead($this->id);
            } else {
                $this->emailCount = EmailRepository::getEmailCountByCase($this->id);
            }
        } else {
            $column = $this->type === self::TYPE_LEAD ? 'e_lead_id' : 'e_case_id';
            $this->emailCount = (int) Email::find()
                ->where([$column => $this->id])
                ->cache($this->cacheDuration)
                ->count();
        }

        return $this;
    }

    /**
     * @return $this
     */
    public function setSmsCount(): StatisticsHelper
    {
        $column = $this->type === self::TYPE_LEAD ? 's_lead_id' : 's_case_id';
        $this->smsCount = (int) Sms::find()
            ->where([$column => $this->id])
            ->cache($this->cacheDuration)
            ->count();
        return $this;
    }

    /**
     * @return $this
     */
    public function setCallCount(): StatisticsHelper
    {
        if ($this->type === self::TYPE_LEAD) {
            $this->callCount = $this->getLeadCallCount();
        } else {
            $this->callCount = $this->getCaseCallCount();
        }
        return $this;
    }

    /**
     * @param bool $onlyParent
     * @return int
     */
    protected function getLeadCallCount(bool $onlyParent = true): int
    {
        $query = CallLogLead::find()
            ->innerJoin(
                CallLog::tableName(),
                CallLog::tableName() . '.cl_id = ' . CallLogLead::tableName() . '.cll_cl_id'
            )
            ->where(['cll_lead_id' => $this->id])
            ->andWhere(['IN', 'cl_type_id', [CallLogType::IN, CallLogType::OUT]])
            ->cache($this->cacheDuration);
        return (int) $query->count();
    }

    /**
    * @param bool $onlyParent
    * @return int
    */
    protected function getCaseCallCount(bool $onlyParent = true): int
    {
        $query = CallLogCase::find()
            ->innerJoin(
                CallLog::tableName(),
                CallLog::tableName() . '.cl_id = ' . CallLogCase::tableName() . '.clc_cl_id'
            )
            ->where(['clc_case_id' => $this->id])
            ->andWhere(['IN', 'cl_type_id', [CallLogType::IN, CallLogType::OUT]])
            ->cache($this->cacheDuration);
        return (int) $query->count();
    }

    /**
     * @return $this
     */
    public function setClientChatCount(): StatisticsHelper
    {
        if ($this->type === self::TYPE_LEAD) {
            $this->clientChatCount = (int) ClientChatLead::find()
                ->andWhere(['ccl_lead_id' => $this->id])
                ->cache($this->cacheDuration)
                ->count();
            return $this;
        }
        $this->clientChatCount = (int) ClientChatCase::find()
            ->andWhere(['cccs_case_id' => $this->id])
            ->cache($this->cacheDuration)
            ->count();
        return $this;
    }

    /**
     * @return $this
     */
    public function setCountAll(): StatisticsHelper
    {
        $this->setEmailCount()
            ->setSmsCount()
            ->setCallCount()
            ->setClientChatCount();
        return $this;
    }

    /**
     * @param int $statusId
     * @return bool
     */
    public function isEnableByStatus(int $statusId): bool
    {
        if ($this->type === self::TYPE_LEAD) {
            return ArrayHelper::isIn($statusId, [
                Lead::STATUS_PROCESSING,
                Lead::STATUS_FOLLOW_UP,
                Lead::STATUS_TRASH,
            ]);
        }
        return ArrayHelper::isIn($statusId, [
            CasesStatus::STATUS_PROCESSING,
            CasesStatus::STATUS_FOLLOW_UP,
            CasesStatus::STATUS_SOLVED,
            CasesStatus::STATUS_TRASH,
        ]);
    }

    /**
     * @return int
     */
    public function getCacheDuration(): int
    {
        return $this->cacheDuration;
    }

    /**
     * @param int $cacheDuration
     * @return StatisticsHelper
     */
    public function setCacheDuration($cacheDuration): StatisticsHelper
    {
        $this->cacheDuration = $cacheDuration;
        return $this;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @return bool
     */
    public function isTypeLead(): bool
    {
        return $this->type === self::TYPE_LEAD;
    }

    /**
     * @return bool
     */
    public function isTypeCase(): bool
    {
        return $this->type === self::TYPE_CASE;
    }

    public static function getLastCommunicationByCaseId(int $caseId): array
    {
        $queryEmailIn = (new Query())
            ->select([
                new Expression('"email" AS type'),
                new Expression('"In" AS direction'),
                'e_case_id AS case_id',
                'MAX(e_created_dt) AS created_dt'
            ])
            ->from(Email::tableName())
            ->where(['e_case_id' => $caseId])
            ->andWhere(['e_type_id' => Email::TYPE_INBOX]);

        $queryEmailOut = (new Query())
            ->select([
                new Expression('"email" AS type'),
                new Expression('"Out" AS direction'),
                'e_case_id AS case_id',
                'MAX(e_created_dt) AS created_dt'
            ])
            ->from(Email::tableName())
            ->where(['e_case_id' => $caseId])
            ->andWhere(['e_type_id' => Email::TYPE_OUTBOX]);

        $querySmsIn = (new Query())
            ->select([
                new Expression('"sms" AS type'),
                new Expression('"In" AS direction'),
                's_case_id AS case_id',
                'MAX(s_created_dt) AS created_dt'
            ])
            ->from(Sms::tableName())
            ->where(['s_case_id' => $caseId])
            ->andWhere(['s_type_id' => Sms::TYPE_INBOX]);

        $querySmsOut = (new Query())
            ->select([
                new Expression('"sms" AS type'),
                new Expression('"Out" AS direction'),
                's_case_id AS case_id',
                'MAX(s_created_dt) AS created_dt'
            ])
            ->from(Sms::tableName())
            ->where(['s_case_id' => $caseId])
            ->andWhere(['s_type_id' => Sms::TYPE_OUTBOX]);

        $queryCallIn = (new Query())
            ->select([
                new Expression('"call" AS type'),
                new Expression('"In" AS direction'),
                'clc_case_id AS case_id',
                'MAX(cl_call_created_dt) AS created_dt'
            ])
            ->from(CallLogCase::tableName())
            ->innerJoin(
                CallLog::tableName(),
                CallLog::tableName() . '.cl_id = ' . CallLogCase::tableName() . '.clc_cl_id'
            )
            ->where(['clc_case_id' => $caseId])
            ->andWhere(['cl_type_id' => CallLogType::IN]);

        $queryCallOut = (new Query())
            ->select([
                new Expression('"call" AS type'),
                new Expression('"Out" AS direction'),
                'clc_case_id AS case_id',
                'MAX(cl_call_created_dt) AS created_dt'
            ])
            ->from(CallLogCase::tableName())
            ->innerJoin(
                CallLog::tableName(),
                CallLog::tableName() . '.cl_id = ' . CallLogCase::tableName() . '.clc_cl_id'
            )
            ->where(['clc_case_id' => $caseId])
            ->andWhere(['cl_type_id' => CallLogType::OUT]);

        $unionQuery = (new Query())
            ->from(['union_table' =>
                    $queryCallIn
                    ->union($queryCallOut)
                    ->union($queryEmailIn)
                    ->union($queryEmailOut)
                    ->union($querySmsIn)
                    ->union($querySmsOut)
            ])
            ->all();

        return $unionQuery;
    }
}
