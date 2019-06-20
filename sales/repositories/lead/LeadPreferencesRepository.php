<?php

namespace sales\repositories\lead;

use common\models\LeadPreferences;
use sales\repositories\NotFoundException;

class LeadPreferencesRepository
{
    /**
     * @param int $id
     * @return LeadPreferences
     */
    public function get(int $id): LeadPreferences
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
        if ($preferences->save(false)) {
            return $preferences->id;
        }
        throw new \RuntimeException('Saving error');
    }

    /**
     * @param LeadPreferences $lead
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function remove(LeadPreferences $lead): void
    {
        if (!$lead->delete()) {
            throw new \RuntimeException('Removing error');
        }
    }
}