<?php

namespace sales\model\conference\useCase\statusCallBackEvent;

use common\models\Call;
use common\models\Conference;
use common\models\ConferenceParticipant;
use common\models\Notifications;
use frontend\widgets\newWebPhone\call\socket\HoldMessage;
use frontend\widgets\newWebPhone\call\socket\MuteMessage;
use sales\model\callLog\services\CallLogConferenceTransferService;
use sales\model\conference\service\ConferenceDataService;
use sales\model\conference\socket\SocketCommands;
use yii\helpers\VarDumper;

class ConferenceStatusCallbackHandler
{
    public function start(Conference $conference, ConferenceStatusCallbackForm $form): void
    {
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
        $conference->end($form->getFormattedTimestamp());

        if (!$conference->save()) {
            \Yii::error(VarDumper::dumpAsString([
                'errors' => $conference->getErrors(),
                'model' => $conference->getAttributes(),
            ]), 'ConferenceStatusCallbackHandler:end');
        }

//        if (!$call = $conference->call) {
//            return;
//        }
//        $service = \Yii::createObject(CallLogConferenceTransferService::class);
//        $service->transfer($call, $conference);
    }

    public function join(Conference $conference, ConferenceStatusCallbackForm $form): void
    {
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

        if (!$data = ConferenceDataService::getDataById($participant->cp_cf_id)) {
            return;
        }

        SocketCommands::sendToAllUsers($data);
    }

    public function hold(Conference $conference, ConferenceStatusCallbackForm $form): void
    {
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

        if ($participant->isAgent() && ($call = $participant->cpCall) && $call->c_created_user_id) {
            Notifications::publish(HoldMessage::COMMAND, ['user_id' => $call->c_created_user_id], HoldMessage::hold($call));
        }
    }

    public function unHold(Conference $conference, ConferenceStatusCallbackForm $form): void
    {
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

        if ($participant->isAgent() && ($call = $participant->cpCall) && $call->c_created_user_id) {
            Notifications::publish(HoldMessage::COMMAND, ['user_id' => $call->c_created_user_id], HoldMessage::unhold($call));
        }
    }

    public function mute(Conference $conference, ConferenceStatusCallbackForm $form): void
    {
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

        if ($participant->isAgent() && ($call = $participant->cpCall) && $call->c_created_user_id) {
            Notifications::publish(MuteMessage::COMMAND, ['user_id' => $call->c_created_user_id], MuteMessage::mute($call));
        }
    }

    public function unMute(Conference $conference, ConferenceStatusCallbackForm $form): void
    {
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

        if ($participant->isAgent() && ($call = $participant->cpCall) && $call->c_created_user_id) {
            Notifications::publish(MuteMessage::COMMAND, ['user_id' => $call->c_created_user_id], MuteMessage::unmute($call));
        }
    }

    private function createParticipant(Conference $conference, ConferenceStatusCallbackForm $form): ConferenceParticipant
    {
        $participant = new ConferenceParticipant();
        $participant->cp_type_id = $form->participant_type_id;
        $participant->cp_cf_id = $conference->cf_id;
        $participant->cp_call_sid = $form->CallSid;
        if ($call = $this->findAndUpdateCall($form->CallSid, $conference)) {
            $participant->cp_call_id = $call->c_id;
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
