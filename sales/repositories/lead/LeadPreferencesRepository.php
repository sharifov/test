<?php

namespace sales\repositories\lead;

use common\models\LeadPreferences;
use sales\dispatchers\EventDispatcher;
use sales\repositories\NotFoundException;

class LeadPreferencesRepository
{

    private $eventDispatcher;

    public function __construct(EventDispatcher $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @param $id
     * @return LeadPreferences
     */
    public function get($id): LeadPreferences
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