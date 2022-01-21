<?php

namespace src\repositories\lead;

use common\models\LeadPreferences;
use src\dispatchers\EventDispatcher;
use src\repositories\NotFoundException;

/**
 * Class LeadPreferencesRepository
 */
class LeadPreferencesRepository
{
    private $eventDispatcher;

    /**
     * LeadPreferencesRepository constructor.
     * @param EventDispatcher $eventDispatcher
     */
    public function __construct(EventDispatcher $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @param $id
     * @return LeadPreferences
     */
    public function find($id): LeadPreferences
    {
        if ($preferences = LeadPreferences::findOne($id)) {
            return $preferences;
        }
        throw new NotFoundException('Lead preferences is not found');
    }

    /**
     * @param LeadPreferences $preferences
     * @return int
     */
    public function save(LeadPreferences $preferences): int
    {
        if (!$preferences->save(false)) {
            throw new \RuntimeException('Saving error');
        }
        $this->eventDispatcher->dispatchAll($preferences->releaseEvents());
        return $preferences->id;
    }

    /**
     * @param LeadPreferences $preferences
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function remove(LeadPreferences $preferences): void
    {
        if (!$preferences->delete()) {
            throw new \RuntimeException('Removing error');
        }
        $this->eventDispatcher->dispatchAll($preferences->releaseEvents());
    }
}
