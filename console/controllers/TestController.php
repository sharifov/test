<?php

namespace console\controllers;

use common\models\Lead;
use common\models\Call;
use common\models\Notifications;
use Faker\Factory;
use modules\twilio\src\entities\conferenceLog\ConferenceLog;
use sales\model\client\useCase\excludeInfo\ClientExcludeIpChecker;
use sales\model\clientChat\cannedResponse\entity\ClientChatCannedResponse;
use sales\model\clientChat\cannedResponseCategory\entity\ClientChatCannedResponseCategory;
use sales\model\clientChat\entity\ClientChat;
use sales\model\clientChat\entity\projectConfig\ClientChatProjectConfig;
use sales\model\clientChat\entity\projectConfig\ProjectConfigApiResponseDto;
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
use sales\model\conference\useCase\PrepareCurrentCallsForNewCall;
use sales\model\conference\useCase\statusCallBackEvent\ConferenceStatusCallbackForm;
use sales\model\project\entity\params\Params;
use sales\services\clientChatMessage\ClientChatMessageService;
use sales\services\clientChatUserAccessService\ClientChatUserAccessService;
use yii\console\Controller;
use yii\helpers\ArrayHelper;
use yii\helpers\Console;
use yii\console\ExitCode;
use yii\helpers\Json;
use yii\helpers\VarDumper;

class TestController extends Controller
{
    public function actionDisconnectCalls()
    {
        $userId = 295;
        $prepare = new PrepareCurrentCallsForNewCall($userId);
        $prepare->prepare();
    }

    public function actionTest()
    {
        VarDumper::dump(Params::default());
        die;
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

    public function actionVirtualCronTest()
    {
        echo date('Y-m-d H:i:s');
        \Yii::info($_SERVER, 'info\TestController:actionTest');
        return ExitCode::OK;
    }

    public function actionFillChatCannedTablesWithTestData()
    {
        echo Console::renderColoredString('%g --- Start %w[' . date('Y-m-d H:i:s') . '] %g' .
            self::class . ':' . __FUNCTION__ . ' %n'), PHP_EOL;

        $processedCategory = 0;
        $timeStart = microtime(true);


//        $categoryCounts = 10000 * 2;
//        Console::startProgress(0, $categoryCounts, 'Counting categories: ', false);
//        $faker = Factory::create('ru_RU');
//        for ($i = 1; $i <= $categoryCounts; $i++) {
//            $cannedResponseCategory = new ClientChatCannedResponseCategory();
//            $cannedResponseCategory->crc_name = $faker->sentence(3, true);
//            $cannedResponseCategory->crc_enabled = 1;
//            $cannedResponseCategory->crc_created_dt = date('Y-m-d H:i:s');
//            $cannedResponseCategory->crc_updated_dt = date('Y-m-d H:i:s');
//            $cannedResponseCategory->save();
//
//            Console::updateProgress($i, $categoryCounts);
//        }
//        Console::endProgress("Categories created." . PHP_EOL);


        $cannedResponseCnt = 10000 * 2;
        Console::startProgress(0, $cannedResponseCnt, 'Counting canned responses: ', false);
        $faker = Factory::create('ru_RU');
        for ($i = 1; $i <= $cannedResponseCnt; $i++) {
            $cannedResponse = new ClientChatCannedResponse();
            $cannedResponse->cr_language_id = 'ru-RU';
            $cannedResponse->cr_user_id = 464;
            $cannedResponse->cr_sort_order = $i;
            $cannedResponse->cr_message = $faker->realText(1000);
            $cannedResponse->cr_created_dt = date('Y-m-d H:i:s');
            $cannedResponse->cr_updated_dt = date('Y-m-d H:i:s');
            $cannedResponse->save();

            Console::updateProgress($i, $cannedResponseCnt);
        }
        Console::endProgress("Canned responses created." . PHP_EOL);

        $timeEnd = microtime(true);
        $time = number_format(round($timeEnd - $timeStart, 2), 2);

        echo Console::renderColoredString('%g --- Execute Time: %w[' . $time .
            ' s] %g ProcessedCategory: %w[' . $processedCategory . '] %g %n'), PHP_EOL;

        echo Console::renderColoredString('%g --- End : %w[' . date('Y-m-d H:i:s') . '] %g' .
            self::class . ':' . __FUNCTION__ . ' %n'), PHP_EOL;
    }


    public function actionTestLead($leadId)
    {
        $lead = Lead::findOne(['id' => $leadId]);

        if (!$lead) {
            echo Console::renderColoredString('%r --- Error: Lead not found by id ' . $leadId . '   %r' . ' %n', true), PHP_EOL;
        }

        $additionalInfo = $lead->getAdditionalInformationFormFirstElement();

        $additionalInfo->vtf_processed = true;
        $additionalInfo->tkt_processed = false;
        $additionalInfo->exp_processed = true;

        $lead->additional_information = Json::encode(ArrayHelper::toArray($additionalInfo));

        if (!$lead->save()) {
            echo Console::renderColoredString('%r --- Error: Lead not saved %r' . ' %n', true), PHP_EOL;
        }

        $lead->sendNotifOnProcessingStatusChanged();
    }

    public function actionShowChatProjectConfigDto($projectId)
    {
        $config = ClientChatProjectConfig::findOne(['ccpc_project_id' => (int)$projectId]);
        if (!$config) {
            echo Console::renderColoredString('%r --- Error: project config not found %r' . ' %n', true), PHP_EOL;
            exit;
        }

        $dto = new ProjectConfigApiResponseDto($config);
        echo $dto->endpoint . PHP_EOL;
	}
	
    /**
     * @throws \Exception
     */
    public function actionTestCentrifugo()
    {
        printf("\n --- Start %s ---\n", $this->ansiFormat(self::class . ' - ' . $this->action->id, Console::FG_YELLOW));
        $n = 0;
        while (true) {
            $n++;
            $calls = Call::find()
                ->where(['c_status_id' => [Call::STATUS_IVR, Call::STATUS_QUEUE, Call::STATUS_COMPLETED]])
                ->andWhere('c_id > FLOOR(RAND()*(3368194-100000)+100000)')
                //->orderBy('RAND()')
                ->limit(rand(1, 2))->all(); //['c_id' => SORT_DESC]
            foreach ($calls as $call) {
                $call->c_status_id = random_int(1, 12);
                $call->c_source_type_id = random_int(1, 12);
                $call->c_updated_dt = date("Y-m-d H:i:s", strtotime('-' . random_int(1, 60) . ' minutes'));
                $call->c_created_dt = $call->c_updated_dt;
                $call->c_queue_start_dt = date("Y-m-d H:i:s", strtotime('-' . random_int(1, 15) . ' minutes'));
                $call->sendFrontendData('update');
                echo ' - ' . $call->c_id . PHP_EOL;
            }
            usleep(0.9 * 1000000);
            if ($n > 100000) {
                break;
            }
        }
        printf("\n --- End %s ---\n", $this->ansiFormat(self::class . ' - ' . $this->action->id, Console::FG_YELLOW));
    }
}
