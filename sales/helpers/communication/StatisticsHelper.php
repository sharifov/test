<?php

namespace sales\helpers\communication;

use common\models\Call;
use common\models\Email;
use common\models\Lead;
use common\models\Sms;
use sales\entities\cases\Cases;
use sales\entities\cases\CasesStatus;
use sales\model\callLog\entity\callLog\CallLog;
use sales\model\callLog\entity\callLog\CallLogType;
use sales\model\callLog\entity\callLogCase\CallLogCase;
use sales\model\callLog\entity\callLogLead\CallLogLead;
use sales\model\clientChat\entity\ClientChat;
use sales\model\clientChatLead\entity\ClientChatLead;
use Yii;
use yii\db\Expression;
use yii\helpers\ArrayHelper;

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
        $column = $this->type === self::TYPE_LEAD ? 'e_lead_id' : 'e_case_id';
        $this->emailCount = (int) Email::find()
            ->where([$column => $this->id])
            ->cache($this->cacheDuration)
            ->count();
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
        if ((bool) Yii::$app->params['settings']['new_communication_block_lead']) {
            $query = CallLogLead::find()
                ->innerJoin(CallLog::tableName(),
                    CallLog::tableName() . '.cl_id = ' . CallLogLead::tableName() . '.cll_cl_id')
                ->where(['cll_lead_id' => $this->id])
                ->andWhere(['IN', 'cl_type_id', [CallLogType::IN, CallLogType::OUT]])
                ->cache($this->cacheDuration);
            if ($onlyParent) {
                $query->andWhere(['cl_group_id' => null]);
            }
            return (int) $query->count();
        }

        $query = Call::find()
            ->where(['c_lead_id' => $this->id])
            ->andWhere(['IN', 'c_call_type_id', [Call::CALL_TYPE_IN, Call::CALL_TYPE_OUT]])
            ->cache($this->cacheDuration);
        if ($onlyParent) {
            $query->andWhere(['c_parent_id' => null]);
        }
        return (int) $query->count();
    }

    /**
    * @param bool $onlyParent
    * @return int
    */
    protected function getCaseCallCount(bool $onlyParent = true): int
    {
        if ((bool) Yii::$app->params['settings']['new_communication_block_lead']) {
            $query = CallLogCase::find()
                ->innerJoin(CallLog::tableName(),
                    CallLog::tableName() . '.cl_id = ' . CallLogCase::tableName() . '.clc_cl_id')
                ->where(['clc_case_id' => $this->id])
                ->andWhere(['IN', 'cl_type_id', [CallLogType::IN, CallLogType::OUT]])
                ->cache($this->cacheDuration);
            if ($onlyParent) {
                $query->andWhere(['cl_group_id' => null]);
            }
            return (int) $query->count();
        }

        $query = Call::find()
            ->where(['c_case_id' => $this->id])
            ->andWhere(['IN', 'c_call_type_id', [Call::CALL_TYPE_IN, Call::CALL_TYPE_OUT]])
            ->cache($this->cacheDuration);
        if ($onlyParent) {
            $query->andWhere(['c_parent_id' => null]);
        }
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

        $this->clientChatCount = (int) ClientChat::find()
            ->where(['cch_case_id' => $this->id])
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
}
