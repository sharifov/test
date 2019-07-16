<?php

namespace sales\repositories\lead;

use common\models\Lead;
use sales\dispatchers\EventDispatcher;
use sales\repositories\NotFoundException;

class LeadRepository
{
    private $eventDispatcher;

    public function __construct(EventDispatcher $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @param $id
     * @return Lead
     */
    public function get($id): Lead
    {
        if ($lead = Lead::findOne($id)) {
            return $lead;
        }
        throw new NotFoundException('Lead is not found');
    }

    /**
     * @param $gid
     * @return Lead
     */
    public function getByGid($gid): Lead
    {
        if ($lead = Lead::findOne(['gid' => $gid])) {
            return $lead;
        }
        throw new NotFoundException('Lead is not found');
    }

    /**
     * @param $requestHash
     * @return Lead|null
     */
    public function getByRequestHash($requestHash):? Lead
    {
        return Lead::find()
            ->where(['l_request_hash' => $requestHash])
            ->andWhere(['>=', 'created', date('Y-m-d H:i:s', strtotime('-12 hours'))])
            ->orderBy(['id' => SORT_ASC])
            ->limit(1)
            ->one();
    }

    /**
     * @param Lead $lead
     * @return int
     */
    public function save(Lead $lead): int
    {
        $lead->setDisableAfterSave();

        if (!$lead->save(false)) {
            throw new \RuntimeException('Saving error');
        }
        $this->eventDispatcher->dispatchAll($lead->releaseEvents());
        return $lead->id;
    }

    /**
     * @param Lead $lead
     */
    public function updateOnlyTripType(Lead $lead): void
    {
        if (!$lead->updateAttributes(['trip_type'])) {
            throw new \RuntimeException('Update trip type error');
        }
    }

    /**
     * @param Lead $lead
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function remove(Lead $lead): void
    {
        if (!$lead->delete()) {
            throw new \RuntimeException('Removing error');
        }
        $this->eventDispatcher->dispatchAll($lead->releaseEvents());
    }
}