<?php

namespace sales\helpers\communication;

use common\models\Call;
use common\models\Email;
use common\models\Sms;
use sales\model\callLog\entity\callLog\CallLog;
use sales\model\callLog\entity\callLogLead\CallLogLead;
use sales\model\clientChat\entity\ClientChat;
use Yii;
use yii\db\Expression;
use yii\helpers\ArrayHelper;

/**
 * Class CommunicationStatisticsHelper
 * @property int $id
 */
class CommunicationStatisticsHelper
{
    public int $emailCount = 0;
    public int $smsCount = 0;
    public int $callCount = 0;
    public int $clientChatCount = 0;

    protected $id;
    protected string $type = 'lead';
    protected int $cacheDuration = 60 * 20; // noCache mode = "-1"
    protected const ALLOWED_TYPES = ['lead', 'case'];

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
    public function setEmailCount(): CommunicationStatisticsHelper
    {
        $column = $this->type === 'lead' ? 'e_lead_id' : 'e_case_id';
        $this->emailCount = (int) Email::find()
            ->where([$column => $this->id])
            ->cache($this->cacheDuration)
            ->count();
        return $this;
    }

    /**
     * @return $this
     */
    public function setSmsCount(): CommunicationStatisticsHelper
    {
        $column = $this->type === 'lead' ? 's_lead_id' : 's_case_id';
        $this->smsCount = (int) Sms::find()
            ->where([$column => $this->id])
            ->cache($this->cacheDuration)
            ->count();
        return $this;
    }

    /**
     * @return $this
     */
    public function setCallCount(): CommunicationStatisticsHelper
    {
        if ($this->type === 'lead') {
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
                ->select(['id' => new Expression('cl_group_id')])
                ->addSelect(['lead_id' => 'call_log_lead.cll_lead_id'])
                ->innerJoin(CallLog::tableName(), 'call_log.cl_id = call_log_lead.cll_cl_id')
                ->where(['cll_lead_id' => $this->id])
                ->groupBy(['id', 'lead_id'])
                ->cache($this->cacheDuration)
                ->count();
        }
        return (int) Call::find()
            ->select(['id' => new Expression('if (c_parent_id IS NULL, c_id, c_parent_id)')])
            ->where(['c_lead_id' => $this->id])
            ->addGroupBy(['id'])
            ->cache($this->cacheDuration)
            ->count();
    }

    /**
     * @return int
     */
    protected function getCaseCallCount(): int
     {
        return (int) Call::find()
            ->select(['id' => new Expression('if (c_parent_id IS NULL, c_id, c_parent_id)')])
            ->where(['c_case_id' => $this->id])
            ->addGroupBy(['id'])
            ->cache($this->cacheDuration)
            ->count();
    }

    /**
     * @return $this
     */
    public function setClientChatCount(): CommunicationStatisticsHelper
    {
        $column = $this->type === 'lead' ? 'cch_lead_id' : 'cch_case_id';
        $this->clientChatCount = (int) ClientChat::find()
            ->where([$column => $this->id])
            ->cache($this->cacheDuration)
            ->count();
        return $this;
    }

    /**
     * @return $this
     */
    public function setCountAll(): CommunicationStatisticsHelper
    {
        $this->setEmailCount()
            ->setSmsCount()
            ->setCallCount()
            ->setClientChatCount();
        return $this;
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
     * @return CommunicationStatisticsHelper
     */
    public function setCacheDuration($cacheDuration): CommunicationStatisticsHelper
    {
        $this->cacheDuration = $cacheDuration;
        return $this;
    }
}
