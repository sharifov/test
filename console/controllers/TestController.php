<?php

namespace console\controllers;

use common\models\Notifications;
use Faker\Factory;
use modules\twilio\src\entities\conferenceLog\ConferenceLog;
use sales\model\clientChat\entity\ClientChat;
use sales\model\clientChat\useCase\create\ClientChatRepository;
use sales\model\clientChatMessage\ClientChatMessageRepository;
use sales\model\clientChatMessage\entity\ClientChatMessage;
use sales\model\clientChatRequest\entity\ClientChatRequest;
use sales\model\clientChatRequest\useCase\api\create\ClientChatRequestApiForm;
use sales\model\clientChatRequest\useCase\api\create\ClientChatRequestService;
use sales\model\clientChatUnread\entity\ClientChatUnread;
use sales\model\clientChatVisitorData\entity\ClientChatVisitorData;
use sales\model\conference\entity\aggregate\ConferenceLogAggregate;
use sales\model\conference\entity\aggregate\Duration;
use sales\model\conference\entity\aggregate\log\HtmlFormatter;
use sales\model\conference\entity\conferenceEventLog\ConferenceEventLog;
use sales\model\conference\entity\conferenceEventLog\EventFactory;
use sales\model\conference\entity\conferenceEventLog\events\ParticipantJoin;
use sales\model\conference\useCase\statusCallBackEvent\ConferenceStatusCallbackForm;
use sales\services\clientChatMessage\ClientChatMessageService;
use sales\services\clientChatUserAccessService\ClientChatUserAccessService;
use yii\console\Controller;
use yii\helpers\VarDumper;

class TestController extends Controller
{
    public function actionTest()
    {

        $data = [
            'id' => '5c23460e-6fc1-4ea6-a368-df52ca5b293e',
            'rid' => 'b1ee59aa-5315-4714-88ee-4487f7ccca31',
            'token' => '86f8680b-5c6c-4c57-89ea-cfdb768ef63d',
            'visitor' => [
                'id' => '86f8680b-5c6c-4c57-89ea-cfdb768ef63d',
                'username' => 'guest-1100',
                'phone' => null,
                'token' => '86f8680b-5c6c-4c57-89ea-cfdb768ef63d',
            ],
            'agent' => [
                'name' => 'bot',
                'username' => 'bot',
                'email' => 'bot@techork.com',
            ],
            'msg' => 'Open-source',
            'timestamp' => 1601728481909,
            'u' => [
                '_id' => 'mBhSE9dtz5i5shAdH',
                'username' => 'guest-1100',
            ],
        ];
        $form = (new ClientChatRequestApiForm())->fillIn('GUEST_UTTERED', $data);

        $clientChatRepository = \Yii::createObject(ClientChatRepository::class);
        $clientChat = $clientChatRepository->findByRid($form->data['rid'] ?? '');
        $message = ClientChatMessage::createByApi($form, $clientChat, new ClientChatRequest());

        $tr = \Yii::$app->get('db_postgres')->beginTransaction();
        try {
            $clientChatMessageRepository = \Yii::createObject(ClientChatMessageRepository::class);
            $clientChatMessageRepository->save($message, 0);

            $tr->commit();
        } catch (\Throwable $e) {
            $tr->rollBack();
        }
        die;




        $chat = ClientChat::findOne(9);
        $chat->cch_status_id = ClientChat::STATUS_TRANSFER;
        $chat->assignOwner(167);
        $repo = \Yii::createObject(ClientChatRepository::class);
        $repo->save($chat);
die;

        $service = \Yii::createObject(ClientChatMessageService::class);
        VarDumper::dump($service->increaseUnreadMessages(9, 295));
        die;


        $unread = ClientChatUnread::find()->select(['*', 'cch_owner_user_id as ownerId'])->andWhere(['ccu_cc_id' => 8])->innerJoinWith('chat', false)->one();
        VarDumper::dump($unread);
        die;

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
