<?php

namespace sales\helpers\communication;

use common\models\Call;
use common\models\Email;
use common\models\Lead;
use common\models\Sms;
use sales\entities\cases\Cases;
use sales\entities\cases\CasesStatus;
use sales\model\callLog\entity\callLog\CallLog;
use sales\model\callLog\entity\callLogLead\CallLogLead;
use sales\model\clientChat\entity\ClientChat;
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
     * @return int
     */
    protected function getLeadCallCount(): int
    {
        if ((bool) Yii::$app->params['settings']['new_communication_block_lead']) {
            return (int) CallLogLead::find()
                ->innerJoin(CallLog::tableName(), 'call_log.cl_id = call_log_lead.cll_cl_id')
                ->where(['cll_lead_id' => $this->id])
                ->cache($this->cacheDuration)
                ->count();
        }
        return (int) Call::find()
            ->where(['c_lead_id' => $this->id])
            ->cache($this->cacheDuration)
            ->count();
    }

    /**
     * @return int
     */
    protected function getCaseCallCount(): int
     {
        return (int) Call::find()
            ->where(['c_case_id' => $this->id])
            ->cache($this->cacheDuration)
            ->count();
    }

    /**
     * @return $this
     */
    public function setClientChatCount(): StatisticsHelper
    {
        $column = $this->type === self::TYPE_LEAD ? 'cch_lead_id' : 'cch_case_id';
        $this->clientChatCount = (int) ClientChat::find()
            ->where([$column => $this->id])
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
}
