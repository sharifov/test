<?php

namespace sales\model\conference\entity\conferenceEventLog;

class ConferenceEventLogRepository
{
    public function save(ConferenceEventLog $log): void
    {
        if (!$log->save(false)) {
            throw new \RuntimeException('Saving error');
        }
    }
}
