<?php

namespace sales\repositories\lead;

use common\models\Lead;
use sales\dispatchers\EventDispatcher;
use sales\repositories\NotFoundException;
use sales\repositories\Repository;

/**
 * Class LeadRepository
 * @property EventDispatcher $eventDispatcher
 * @method null|Lead get(int $id)
 * @method null|Lead getByGid(string $gid)
 */
class LeadRepository extends Repository
{
    private $eventDispatcher;

    /**
     * LeadRepository constructor.
     * @param EventDispatcher $eventDispatcher
     */
    public function __construct(EventDispatcher $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @param int $id
     * @return Lead
     */
    public function find(int $id): Lead
    {
        if ($lead = Lead::findOne($id)) {
            return $lead;
        }
        throw new NotFoundException('Lead is not found');
    }

    /**
     * @param string $gid
     * @return Lead
     */
    public function findByGid(string $gid): Lead
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
     * @param int $current
     * @return array
     */
    public function getActiveAll(int $current): array
    {
        return Lead::find()
            ->select(['id'])
            ->where([
                'status' => [
                    Lead::STATUS_ON_HOLD, Lead::STATUS_PROCESSING,
                    Lead::STATUS_SNOOZE, Lead::STATUS_FOLLOW_UP
                ]
            ])->andWhere(['<>', 'id', $current])->asArray()->all();
    }

    /**
     * @param Lead $lead
     * @return int
     */
    public function save(Lead $lead): int
    {
        $lead->disableAREvents();

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