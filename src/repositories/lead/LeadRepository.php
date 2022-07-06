<?php

namespace src\repositories\lead;

use common\models\Lead;
use src\dispatchers\EventDispatcher;
use src\model\lead\LeadCodeException;
use src\repositories\NotFoundException;
use yii\db\Expression;

/**
 * Class LeadRepository
 * @property EventDispatcher $eventDispatcher
 */
class LeadRepository
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
        throw new NotFoundException('Lead is not found', LeadCodeException::LEAD_NOT_FOUND);
    }

    public function get(int $id): ?Lead
    {
        try {
            return $this->find($id);
        } catch (NotFoundException $e) {
            return null;
        }
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
        throw new NotFoundException('Lead is not found', LeadCodeException::LEAD_NOT_FOUND);
    }

    /**
     * @param string $uid
     * @return Lead
     */
    public function findByUid(string $uid): Lead
    {
        if ($lead = Lead::findOne(['uid' => $uid])) {
            return $lead;
        }
        throw new NotFoundException('Lead is not found', LeadCodeException::LEAD_NOT_FOUND);
    }

    public function getByUid(string $uid): ?Lead
    {
        try {
            return $this->findByUid($uid);
        } catch (NotFoundException $e) {
            return null;
        }
    }

    /**
     * @param $requestHash
     * @return Lead|null
     */
    public function getByRequestHash($requestHash): ?Lead
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
     * @param int $clientId
     * @return null|Lead
     */
    public function getActiveByClientId(int $clientId): ?Lead
    {
        return Lead::find()
            ->where(['client_id' => $clientId,
                     'status' => [
                        Lead::STATUS_PROCESSING, Lead::STATUS_SNOOZE,
                        Lead::STATUS_ON_HOLD, Lead::STATUS_FOLLOW_UP]
            ])
            ->orderBy(['id' => SORT_DESC])
            ->limit(1)
            ->one();
    }

    /**
     * @param int $clientId
     * @return null|Lead
     */
    public function getByClientId(int $clientId): ?Lead
    {
        return Lead::find()
            ->where(['client_id' => $clientId])
            ->orderBy(['id' => SORT_DESC])
            ->limit(1)
            ->one();
    }

    /**
     * @param Lead $lead
     * @return int
     */
    public function save(Lead $lead): int
    {
        $lead->disableAREvents();

        if (!$lead->save(false)) {
            throw new \RuntimeException('Saving error', LeadCodeException::LEAD_SAVE);
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
            throw new \RuntimeException('Update trip type error', LeadCodeException::LEAD_UPDATE_TRIP_TYPE);
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
            throw new \RuntimeException('Removing error', LeadCodeException::LEAD_REMOVE);
        }
        $this->eventDispatcher->dispatchAll($lead->releaseEvents());
    }

    public function findByIdAndNotEmptyBoFlightId(int $id): Lead
    {
        if (
            $lead = Lead::find()
            ->andWhere(['id' => $id])
            ->andWhere(new Expression("(bo_flight_id is not null and bo_flight_id <> 0)"))->one()
        ) {
            return $lead;
        }
        throw new NotFoundException('Lead with Sale is not found', LeadCodeException::LEAD_NOT_FOUND);
    }
}
