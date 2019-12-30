<?php

namespace sales\repositories\lead;

use common\models\LeadFlightSegment;
use sales\dispatchers\EventDispatcher;
use sales\model\lead\LeadCodeException;
use sales\repositories\NotFoundException;
use sales\repositories\Repository;

/**
 * Class LeadSegmentRepository
 *
 * @method null|LeadFlightSegment get($id)
 */
class LeadSegmentRepository extends Repository
{
    private $eventDispatcher;

    /**
     * LeadSegmentRepository constructor.
     * @param EventDispatcher $eventDispatcher
     */
    public function __construct(EventDispatcher $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @param $id
     * @return LeadFlightSegment
     */
    public function find($id): LeadFlightSegment
    {
        if ($segment = LeadFlightSegment::findOne($id)) {
            return $segment;
        }
        throw new NotFoundException('FlightSegment is not found.', LeadCodeException::SEGMENT_NOT_FOUND);
    }

    /**
     * @param LeadFlightSegment $segment
     * @return int
     */
    public function save(LeadFlightSegment $segment): int
    {
        if (!$segment->save(false)) {
            throw new \RuntimeException('Saving error.', LeadCodeException::SEGMENT_SAVE);
        }
        $this->eventDispatcher->dispatchAll($segment->releaseEvents());
        return $segment->id;
    }

    /**
     * @param LeadFlightSegment $segment
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function remove(LeadFlightSegment $segment): void
    {
        if (!$segment->delete()) {
            throw new \RuntimeException('Removing error.', LeadCodeException::SEGMENT_REMOVE);
        }
        $this->eventDispatcher->dispatchAll($segment->releaseEvents());
    }

    /**
     * @param array $segments
     * @param array $newIds
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function removeOld(array $segments, array $newIds): void
    {
        /** @var LeadFlightSegment $segment */
        foreach ($segments as $segment) {
            if (!in_array($segment->id, $newIds)) {
                $this->remove($segment);
            }
        }
    }
}
