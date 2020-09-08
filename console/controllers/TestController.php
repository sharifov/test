<?php

namespace console\controllers;

use modules\twilio\src\entities\conferenceLog\ConferenceLog;
use sales\model\conference\entity\aggregate\ConferenceLogAggregate;
use sales\model\conference\entity\conferenceEventLog\ConferenceEventLog;
use sales\model\conference\entity\conferenceEventLog\EventFactory;
use sales\model\conference\entity\conferenceEventLog\events\ParticipantJoin;
use sales\model\conference\useCase\statusCallBackEvent\ConferenceStatusCallbackForm;
use yii\console\Controller;
use yii\helpers\VarDumper;

class TestController extends Controller
{
    public function actionTest()
    {
        $conferenceSid  = 'CFe62b8186bd437d1a78d3ca94af79061d';
        $eventsLog = ConferenceEventLog::find()
            ->select(['cel_event_type', 'cel_data'])
            ->andWhere(['cel_conference_sid' => $conferenceSid])->orderBy(['cel_sequence_number' => SORT_ASC])->asArray()->all();
        $events = [];
        foreach ($eventsLog as $item) {
           $events[] = EventFactory::create($item['cel_event_type'], $item['cel_data']);
        }
        $aggregate = new ConferenceLogAggregate($events);
        $aggregate->run();
        VarDumper::dump($aggregate->getUsersReport());
    }
}
