<?php

namespace sales\repositories\lead;

use common\models\LeadPreferences;
use sales\repositories\NotFoundException;

class LeadPreferencesRepository
{
    public function get($id): LeadPreferences
    {
        if ($preferences = LeadPreferences::findOne($id)) {
            return $preferences;
        }
        throw new NotFoundException('Lead preferences is not found');
    }

    public function save(LeadPreferences $preferences): int
    {
        if ($preferences->save(false)) {
            return $preferences->id;
        }
        throw new \RuntimeException('Saving error');
    }

    public function remove(LeadPreferences $lead): void
    {
        if (!$lead->delete()) {
            throw new \RuntimeException('Removing error');
        }
    }
}