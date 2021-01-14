<?php

namespace sales\repositories\source;

use common\models\Sources;
use sales\dispatchers\EventDispatcher;
use sales\repositories\NotFoundException;

/**
 * Class SourceRepository
 * @property EventDispatcher $eventDispatcher
 */
class SourceRepository
{
    private $eventDispatcher;

    /**
     * SourceRepository constructor.
     * @param EventDispatcher $eventDispatcher
     */
    public function __construct(EventDispatcher $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @param $phone
     * @return Sources
     */
    public function findByPhone($phone): Sources
    {
        if ($source = Sources::findOne(['phone_number' => $phone])) {
            return $source;
        }
        throw new NotFoundException('Source is not found');
    }

    /**
     * @param $id
     * @return Sources
     */
    public function find($id): Sources
    {
        if ($source = Sources::findOne($id)) {
            return $source;
        }
        throw new NotFoundException('Source is not found');
    }

    /**
     * @param Sources $source
     * @return int
     */
    public function save(Sources $source): int
    {
        if (!$source->save(false)) {
            throw new \RuntimeException('Saving error');
        }
        $this->eventDispatcher->dispatchAll($source->releaseEvents());
        return $source->id;
    }
}
