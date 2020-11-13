<?php

namespace sales\model\conference\entity\conferenceEventLog;

class ConferenceEventLogQuery
{
    public static function getRawData(string $conferenceSid): array
    {
        $eventsLog = ConferenceEventLog::find()
            ->select(['cel_event_type as type', 'cel_data as data'])
            ->andWhere(['cel_conference_sid' => $conferenceSid])
            ->orderBy(['cel_sequence_number' => SORT_ASC])
            ->asArray()
            ->all();
        return $eventsLog;
    }
}
