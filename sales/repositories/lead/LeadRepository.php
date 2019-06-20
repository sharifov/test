<?php

namespace sales\repositories\lead;

use common\models\Lead;
use sales\repositories\NotFoundException;

class LeadRepository
{
    /**
     * @param int $id
     * @return Lead
     */
    public function get(int $id): Lead
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
    public function getByGid(string $gid): Lead
    {
        if ($lead = Lead::findOne(['gid' => $gid])) {
            return $lead;
        }
        throw new NotFoundException('Lead is not found');
    }

    /**
     * @param string $requestHash
     * @return Lead|null
     */
    public function getByRequestHash(string $requestHash):? Lead
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
        if ($lead->save(false)) {
            return $lead->id;
        }
        throw new \RuntimeException('Saving error');
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
    }
}