<?php

namespace src\model\conference\useCase\statusCallBackEvent;

use common\components\jobs\UpdateConferenceParticipantCallIdJob;
use common\models\Call;
use common\models\Conference;
use common\models\ConferenceParticipant;
use common\models\Notifications;
use frontend\widgets\newWebPhone\call\socket\HoldMessage;
use frontend\widgets\newWebPhone\call\socket\MuteMessage;
use modules\product\src\entities\productQuoteChange\ProductQuoteChange;
use src\dispatchers\EventDispatcher;
use src\helpers\setting\SettingHelper;
use src\model\callLog\services\CallLogConferenceTransferService;
use src\model\client\notifications\ClientNotificationCanceler;
use src\model\conference\entity\conferenceEventLog\ConferenceEventLog;
use src\model\conference\entity\conferenceEventLog\ConferenceEventLogRepository;
use src\model\conference\entity\conferenceEventLog\events\ConferenceEnd;
use src\model\conference\entity\conferenceEventLog\events\ConferenceStart;
use src\model\conference\entity\conferenceEventLog\events\ParticipantHold;
use src\model\conference\entity\conferenceEventLog\events\ParticipantJoin;
use src\model\conference\entity\conferenceEventLog\events\ParticipantLeave;
use src\model\conference\entity\conferenceEventLog\events\ParticipantMute;
use src\model\conference\entity\conferenceEventLog\events\ParticipantUnHold;
use src\model\conference\entity\conferenceEventLog\events\ParticipantUnMute;
use src\model\conference\service\ConferenceDataService;
use src\model\conference\socket\SocketCommands;
use src\model\conference\useCase\saveParticipantStats\Command;
use src\model\conference\useCase\saveParticipantStats\Handler;
use yii\helpers\VarDumper;

/**
 * Class ConferenceStatusCallbackHandler
 *
 * @property EventDispatcher $eventDispatcher
 * @property ConferenceEventLogRepository $eventLogRepository
 * @property Handler $saveParticipantHandler
 * @property ClientNotificationCanceler $clientNotificationCanceler
 */
class ConferenceStatusCallbackHandler
{
    public const DELAY_JOB_FOR_UPDATE_PARTICIPANT_CALL_ID = 5;

    private EventDispatcher $eventDispatcher;
    private ConferenceEventLogRepository $eventLogRepository;
    private Handler $saveParticipantHandler;
    private ClientNotificationCanceler $clientNotificationCanceler;

    public function __construct(
        EventDispatcher $eventDispatcher,
        ConferenceEventLogRepository $eventLogRepository,
        Handler $saveParticipantHandler,
        ClientNotificationCanceler $clientNotificationCanceler
    ) {
        $this->eventDispatcher = $eventDispatcher;
        $this->eventLogRepository = $eventLogRepository;
        $this->saveParticipantHandler = $saveParticipantHandler;
        $this->clientNotificationCanceler = $clientNotificationCanceler;
    }

    public function start(Conference $conference, ConferenceStatusCallbackForm $form): void
    {
        try {
            $event = ConferenceStart::fromArray($form->toArray());
            $log = ConferenceEventLog::create($form->ConferenceSid, new \DateTimeImmutable(), $event->toJson(), $form->StatusCallbackEvent, $form->SequenceNumber);
            $this->eventLogRepository->save($log);
        } catch (\Throwable $e) {
            \Yii::error($e, static::class . ':start:eventLog');
        }

        $conference->start($form->getFormattedTimestamp());
        if (!$conference->save()) {
            \Yii::error(VarDumper::dumpAsString([
                'errors' => $conference->getErrors(),
                'model' => $conference->getAttributes(),
            ]), 'ConferenceStatusCallbackHandler:start');
        }
    }

    public function end(Conference $conference, ConferenceStatusCallbackForm $form): void
    {
        try {
            $event = ConferenceEnd::fromArray($form->toArray());
            $log = ConferenceEventLog::create($form->ConferenceSid, new \DateTimeImmutable(), $event->toJson(), $form->StatusCallbackEvent, $form->SequenceNumber);
            $this->eventLogRepository->save($log);
        } catch (\Throwable $e) {
            \Yii::error($e, static::class . ':end:eventLog');
        }

        $conference->end($form->getFormattedTimestamp());

        if (!$conference->save()) {
            \Yii::error(VarDumper::dumpAsString([
                'errors' => $conference->getErrors(),
                'model' => $conference->getAttributes(),
            ]), 'ConferenceStatusCallbackHandler:end');
        }

        $this->saveParticipantHandler->handle(new Command($conference->cf_sid, $conference->cf_id));

//        if (!$call = $conference->call) {
//            return;
//        }
//        $service = \Yii::createObject(CallLogConferenceTransferService::class);
//        $service->transfer($call, $conference);
    }

    public function join(Conference $conference, ConferenceStatusCallbackForm $form): void
    {
        try {
            $event = ParticipantJoin::fromArray($form->toArray());
            $log = ConferenceEventLog::create($form->ConferenceSid, new \DateTimeImmutable(), $event->toJson(), $form->StatusCallbackEvent, $form->SequenceNumber);
            $this->eventLogRepository->save($log);
        } catch (\Throwable $e) {
            \Yii::error($e, static::class . ':join:eventLog');
        }

        $participant = $this->createParticipant($conference, $form);
        $participant->join();
        $participant->cp_join_dt = date('Y-m-d H:i:s');
        if (!$participant->save()) {
            \Yii::error(VarDumper::dumpAsString([
                'errors' => $participant->getErrors(),
                'model' => $participant->getAttributes(),
            ]), 'ConferenceStatusCallbackHandler:join');
            return;
        }

        if (!$data = ConferenceDataService::getDataById($participant->cp_cf_id)) {
            return;
        }

        SocketCommands::sendToAllUsers($data);
    }

    public function leave(Conference $conference, ConferenceStatusCallbackForm $form): void
    {
        try {
            $event = ParticipantLeave::fromArray($form->toArray());
            $log = ConferenceEventLog::create($form->ConferenceSid, new \DateTimeImmutable(), $event->toJson(), $form->StatusCallbackEvent, $form->SequenceNumber);
            $this->eventLogRepository->save($log);
        } catch (\Throwable $e) {
            \Yii::error($e, static::class . ':leave:eventLog');
        }

        $participant = ConferenceParticipant::find()->where([
            'cp_cf_id' => $conference->cf_id,
            'cp_call_sid' => $form->CallSid,
        ])->one();

        if (!$participant) {
            $participant = $this->createParticipant($conference, $form);
        }

        $participant->leave(date('Y-m-d H:i:s'));
        if (!$participant->save()) {
            \Yii::error(VarDumper::dumpAsString([
                'errors' => $participant->getErrors(),
                'model' => $participant->getAttributes(),
            ]), 'ConferenceStatusCallbackHandler:leave');
            return;
        }

        if ($participant->isClient() && ($duration = $participant->getDuration()) && $duration >= SettingHelper::clientNotificationSuccessCallMinDuration()) {
            $caseId = Call::find()->select(['c_case_id'])->andWhere(['c_id' => $participant->cp_call_id])->scalar();
            if ($caseId) {
                $productQuoteChanges = ProductQuoteChange::find()->select(['pqc_id'])->byCaseId($caseId)->isNotDecided()->column();
                if ($productQuoteChanges) {
                    foreach ($productQuoteChanges as $productQuoteChangeId) {
                        $this->clientNotificationCanceler->cancel(\src\model\client\notifications\client\entity\NotificationType::PRODUCT_QUOTE_CHANGE_AUTO_DECISION_PENDING_EVENT, $productQuoteChangeId);
                    }
                }
            }
        }

        if (!$data = ConferenceDataService::getDataById($participant->cp_cf_id)) {
            return;
        }

        SocketCommands::sendToAllUsers($data);
    }

    public function hold(Conference $conference, ConferenceStatusCallbackForm $form): void
    {
        try {
            $event = ParticipantHold::fromArray($form->toArray());
            $log = ConferenceEventLog::create($form->ConferenceSid, new \DateTimeImmutable(), $event->toJson(), $form->StatusCallbackEvent, $form->SequenceNumber);
            $this->eventLogRepository->save($log);
        } catch (\Throwable $e) {
            \Yii::error($e, static::class . ':hold:eventLog');
        }

        $participant = ConferenceParticipant::find()->where([
            'cp_cf_id' => $conference->cf_id,
            'cp_call_sid' => $form->CallSid,
        ])->one();

        if (!$participant) {
            $participant = $this->createParticipant($conference, $form);
        }

        $participant->hold(date('Y-m-d H:i:s'));
        if (!$participant->save()) {
            \Yii::error(VarDumper::dumpAsString([
                'errors' => $participant->getErrors(),
                'model' => $participant->getAttributes(),
            ]), 'ConferenceStatusCallbackHandler:hold');
            return;
        }

        if (($participant->isAgent() || $participant->isUser()) && ($call = $participant->cpCall) && $call->c_created_user_id) {
            Notifications::publish(HoldMessage::COMMAND, ['user_id' => $call->c_created_user_id], HoldMessage::hold($call));
        }
    }

    public function unHold(Conference $conference, ConferenceStatusCallbackForm $form): void
    {
        try {
            $event = ParticipantUnHold::fromArray($form->toArray());
            $log = ConferenceEventLog::create($form->ConferenceSid, new \DateTimeImmutable(), $event->toJson(), $form->StatusCallbackEvent, $form->SequenceNumber);
            $this->eventLogRepository->save($log);
        } catch (\Throwable $e) {
            \Yii::error($e, static::class . ':unHold:eventLog');
        }

        $participant = ConferenceParticipant::find()->where([
            'cp_cf_id' => $conference->cf_id,
            'cp_call_sid' => $form->CallSid,
        ])->one();

        if (!$participant) {
            $participant = $this->createParticipant($conference, $form);
        }

        $participant->join();
        if (!$participant->save()) {
            \Yii::error(VarDumper::dumpAsString([
                'errors' => $participant->getErrors(),
                'model' => $participant->getAttributes(),
            ]), 'ConferenceStatusCallbackHandler:unHold');
            return;
        }

        if (($participant->isAgent() || $participant->isUser()) && ($call = $participant->cpCall) && $call->c_created_user_id) {
            Notifications::publish(HoldMessage::COMMAND, ['user_id' => $call->c_created_user_id], HoldMessage::unhold($call));
        }
    }

    public function mute(Conference $conference, ConferenceStatusCallbackForm $form): void
    {
        try {
            $event = ParticipantMute::fromArray($form->toArray());
            $log = ConferenceEventLog::create($form->ConferenceSid, new \DateTimeImmutable(), $event->toJson(), $form->StatusCallbackEvent, $form->SequenceNumber);
            $this->eventLogRepository->save($log);
        } catch (\Throwable $e) {
            \Yii::error($e, static::class . ':mute:eventLog');
        }

        $participant = ConferenceParticipant::find()->where([
            'cp_cf_id' => $conference->cf_id,
            'cp_call_sid' => $form->CallSid,
        ])->one();

        if (!$participant) {
            $participant = $this->createParticipant($conference, $form);
        }

        $participant->mute();
        if (!$participant->save()) {
            \Yii::error(VarDumper::dumpAsString([
                'errors' => $participant->getErrors(),
                'model' => $participant->getAttributes(),
            ]), 'ConferenceStatusCallbackHandler:mute');
            return;
        }

        if (($participant->isAgent() || $participant->isUser()) && ($call = $participant->cpCall) && $call->c_created_user_id) {
            Notifications::publish(MuteMessage::COMMAND, ['user_id' => $call->c_created_user_id], MuteMessage::mute($call));
        }
    }

    public function unMute(Conference $conference, ConferenceStatusCallbackForm $form): void
    {
        try {
            $event = ParticipantUnMute::fromArray($form->toArray());
            $log = ConferenceEventLog::create($form->ConferenceSid, new \DateTimeImmutable(), $event->toJson(), $form->StatusCallbackEvent, $form->SequenceNumber);
            $this->eventLogRepository->save($log);
        } catch (\Throwable $e) {
            \Yii::error($e, static::class . ':unMute:eventLog');
        }

        $participant = ConferenceParticipant::find()->where([
            'cp_cf_id' => $conference->cf_id,
            'cp_call_sid' => $form->CallSid,
        ])->one();

        if (!$participant) {
            $participant = $this->createParticipant($conference, $form);
        }

        $participant->unMute();
        if (!$participant->save()) {
            \Yii::error(VarDumper::dumpAsString([
                'errors' => $participant->getErrors(),
                'model' => $participant->getAttributes(),
            ]), 'ConferenceStatusCallbackHandler:unMute');
            return;
        }

        if (($participant->isAgent() || $participant->isUser()) && ($call = $participant->cpCall) && $call->c_created_user_id) {
            Notifications::publish(MuteMessage::COMMAND, ['user_id' => $call->c_created_user_id], MuteMessage::unmute($call));
        }
    }

    private function createParticipant(Conference $conference, ConferenceStatusCallbackForm $form): ConferenceParticipant
    {
        $participant = new ConferenceParticipant();
        $participant->cp_type_id = $form->participant_type_id;
        $participant->cp_cf_id = $conference->cf_id;
        $participant->cp_cf_sid = $conference->cf_sid;
        $participant->cp_call_sid = $form->CallSid;
        $participant->cp_user_id = $form->participant_user_id;
        $participant->cp_identity = $form->participant_identity;
        if ($call = $this->findAndUpdateCall($form->CallSid, $conference)) {
            $participant->cp_call_id = $call->c_id;
        } else {
            $delayJob = self::DELAY_JOB_FOR_UPDATE_PARTICIPANT_CALL_ID;
            $job = new UpdateConferenceParticipantCallIdJob($participant->cp_call_sid, $participant->cp_cf_sid, $participant->cp_cf_id);
            $job->delayJob = $delayJob;
            \Yii::$app->queue_job->delay($delayJob)->push($job);
        }
        return $participant;
    }

    private function findAndUpdateCall($callSid, Conference $conference): ?Call
    {
        if (!$call = Call::find()->where(['c_call_sid' => $callSid])->one()) {
            return null;
        }

        if ($call->c_conference_id !== $conference->cf_id) {
            $call->c_conference_sid = $conference->cf_sid;
            $call->c_conference_id = $conference->cf_id;
            if (!$call->save()) {
                \Yii::error(VarDumper::dumpAsString([
                    'errors' => $call->getErrors(),
                    'model' => $call->getAttributes(),
                ]), 'ConferenceStatusCallbackHandler:findAndUpdateCall');
            }
        }

        return $call;
    }
}
