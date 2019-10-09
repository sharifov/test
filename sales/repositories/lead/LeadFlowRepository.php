<?php

namespace sales\repositories\lead;

use common\models\LeadFlow;
use sales\dispatchers\EventDispatcher;
use sales\repositories\NotFoundException;
use sales\repositories\Repository;

/**
 * Class LeadFlowRepository
 * @method null|LeadFlow get($id)
 * @method null|LeadFlow getPrevious($leadId)
 */
class LeadFlowRepository extends Repository
{
    private $eventDispatcher;

    /**
     * LeadFlowRepository constructor.
     * @param EventDispatcher $eventDispatcher
     */
    public function __construct(EventDispatcher $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @param $leadId
     * @return LeadFlow
     */
    public function findPrevious($leadId): LeadFlow
    {
        if ($leadFlow = LeadFlow::find()->where(['lead_id' => $leadId])->orderBy(['id' => SORT_DESC])->limit(1)->one()) {
            return $leadFlow;
        }
        throw new NotFoundException('LeadFlow is not found');
    }

    /**
     * @param $id
     * @return LeadFlow
     */
    public function find($id): LeadFlow
    {
        if ($leadFlow = LeadFlow::findOne($id)) {
            return $leadFlow;
        }
        throw new NotFoundException('LeadFlow is not found');
    }

    /**
     * @param LeadFlow $leadFlow
     * @return int
     */
    public function save(LeadFlow $leadFlow): int
    {
        if (!$leadFlow->save(false)) {
            throw new \RuntimeException('Saving error');
        }
        $this->eventDispatcher->dispatchAll($leadFlow->releaseEvents());
        return $leadFlow->id;
    }

    /**
     * @param LeadFlow $leadFlow
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function remove(LeadFlow $leadFlow): void
    {
        if (!$leadFlow->delete()) {
            throw new \RuntimeException('Removing error');
        }
        $this->eventDispatcher->dispatchAll($leadFlow->releaseEvents());
    }
}