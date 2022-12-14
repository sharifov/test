<?php

namespace src\model\conference\useCase\saveParticipantStats;

use src\model\conference\entity\aggregate\ConferenceLogAggregate;
use src\model\conference\entity\conferenceEventLog\ConferenceEventLogQuery;
use src\model\conference\entity\conferenceEventLog\EventFactory;
use src\model\conference\entity\conferenceParticipantStats\ConferenceParticipantStats;
use yii\helpers\VarDumper;

class Handler
{
    public function handle(Command $command): void
    {
        $eventsLog = ConferenceEventLogQuery::getRawData($command->conferenceSid);
        if (!$eventsLog) {
            \Yii::error('Not found conference events. Sid: ' . $command->conferenceSid, 'saveParticipantStats');
            return;
        }

        $events = [];
        foreach ($eventsLog as $item) {
            $events[] = EventFactory::create($item['type'], $item['data']);
        }

        try {
            $aggregate = new ConferenceLogAggregate($events);
            $aggregate->run();
            $participants = $aggregate->getParticipantsResult();
        } catch (\Throwable $e) {
            \Yii::error(
                VarDumper::dumpAsString([
                    'command' => $command,
                    'error' => $e->getMessage(),
                    'message' => 'Aggregation error',
                ]),
                'saveParticipantStats'
            );
            return;
        }

        if (!$participants) {
            \Yii::info([
                'message' => 'During the processing of the final state of the conference, no participants were found',
                'reason' => 'Possible: Conference StatusCallbackEvent = conference-end received before any conference callback',
                'conferenceSid' => $command->conferenceSid,
            ], 'log\saveParticipantStats');
            return;
        }

        foreach ($participants as $participant) {
            try {
                $stats = new ConferenceParticipantStats();
                $stats->cps_cf_id = $command->conferenceId;
                $stats->cps_cf_sid = $command->conferenceSid;
                $stats->cps_participant_identity = $participant['id'];
                $stats->cps_user_id = $participant['userId'];
                $stats->cps_duration = (int)$participant['duration']['value'];
                $stats->cps_talk_time = (int)$participant['talkDuration']['value'];
                $stats->cps_hold_time = (int)$participant['holdDuration']['value'];
                $stats->cps_created_dt = date('Y-m-d H:i:s');
                if (!$stats->save()) {
                    \Yii::error(VarDumper::dumpAsString([
                        'command' => $command,
                        'errors' => $stats->getErrors(),
                        'model' => $stats->getAttributes(),
                    ]), 'saveParticipantStats:ConferenceParticipantStats:save');
                }
            } catch (\Throwable $e) {
                \Yii::error(VarDumper::dumpAsString([
                    'command' => $command,
                    'errors' => $stats->getErrors(),
                    'model' => $stats->getAttributes(),
                    'exception' => $e->getMessage(),
                ]), 'saveParticipantStats:ConferenceParticipantStats:exception');
            }
        }
    }
}
