<?php

namespace console\controllers;

use Faker\Factory;
use modules\twilio\src\entities\conferenceLog\ConferenceLog;
use sales\model\clientChat\useCase\create\ClientChatRepository;
use sales\model\clientChatVisitorData\entity\ClientChatVisitorData;
use sales\model\conference\entity\aggregate\ConferenceLogAggregate;
use sales\model\conference\entity\aggregate\Duration;
use sales\model\conference\entity\aggregate\log\HtmlFormatter;
use sales\model\conference\entity\conferenceEventLog\ConferenceEventLog;
use sales\model\conference\entity\conferenceEventLog\EventFactory;
use sales\model\conference\entity\conferenceEventLog\events\ParticipantJoin;
use sales\model\conference\useCase\statusCallBackEvent\ConferenceStatusCallbackForm;
use sales\services\clientChatUserAccessService\ClientChatUserAccessService;
use yii\console\Controller;
use yii\helpers\VarDumper;

class TestController extends Controller
{
    public function actionTest()
    {

        $conferenceSid  = 'CF2e8a4d3fbbd53c399f5819b55f369fed';
        $eventsLog = ConferenceEventLog::find()
            ->select(['cel_event_type', 'cel_data'])
            ->andWhere(['cel_conference_sid' => $conferenceSid])->orderBy(['cel_sequence_number' => SORT_ASC])->asArray()
            //->limit(10)
            ->all();
        $events = [];
        foreach ($eventsLog as $item) {
           $events[] = EventFactory::create($item['cel_event_type'], $item['cel_data']);
        }
        $aggregate = new ConferenceLogAggregate($events);
        $aggregate->run();
//        VarDumper::dump($aggregate->getParticipantsResult());
        $printer = new HtmlFormatter($aggregate->logs);
        VarDumper::dump($printer->format());


    }

    public function actionTestIsChatTransfer(int $cchId)
	{
		$chatRepository = \Yii::createObject(ClientChatRepository::class);
		var_dump($chatRepository->isChatInTransfer($cchId));
	}

	public function actionTestChatVisitorDataRules()
	{
		$faker = Factory::create();
		$data = new ClientChatVisitorData();
		$data->cvd_referrer = $faker->realText(1005, 1);

		var_dump(strlen($data->cvd_referrer));
		var_dump($data->validate());
		var_dump($data->errors);
	}

	public function actionTestUserAccessToAllChats(int $userId)
	{
		$channelIds = [1,2,3,4,5,6,7,8];
		$service = \Yii::createObject(ClientChatUserAccessService::class);
		$service->setUserAccessToAllChatsByChannelIds($channelIds, $userId);
	}
}
