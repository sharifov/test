<?php

namespace sales\model\sms\service;

use common\models\Notifications;
use frontend\widgets\newWebPhone\sms\socket\Message;
use frontend\widgets\notification\NotificationMessage;
use sales\model\sms\useCase\send\Contact;
use Yii;
use common\models\Sms;
use frontend\widgets\newWebPhone\sms\dto\SmsDto;
use frontend\widgets\newWebPhone\sms\form\SmsSendForm;
use frontend\widgets\newWebPhone\sms\job\SmsSendJob;

class SmsSenderService
{
    public function sendToInternalNumber(SmsSendForm $form): array
    {
        $out = new Sms();
        $out->s_type_id = Sms::TYPE_OUTBOX;
        $out->s_status_id = Sms::STATUS_DONE;
        $out->s_sms_text = $form->text;
        $out->s_phone_from = $form->userPhone;
        $out->s_phone_to = $form->getContactPhone();
        $out->s_created_dt = date('Y-m-d H:i:s');
        $out->s_created_user_id = $form->user->id;
        $out->s_project_id = $form->getProjectId();

        $transaction = \Yii::$app->db->beginTransaction();
        if ($out->save()) {
            $in = new Sms();
            $in->s_type_id = Sms::TYPE_INBOX;
            $in->s_status_id = Sms::STATUS_DONE;
            $in->s_is_new = true;
            $in->s_sms_text = $form->text;
            $in->s_phone_from = $form->userPhone;
            $in->s_phone_to = $form->getContactPhone();
            $in->s_created_dt = date('Y-m-d H:i:s');
            $in->s_created_user_id = $form->getContactId();
            $in->s_project_id = $form->getProjectId();
            $in->s_status_done_dt = date('Y-m-d H:i:s');
            if ($in->save()) {
                $transaction->commit();
                $result['success'] = true;
                if ($ntf = Notifications::create(
                    $form->contactEntity->id,
                    'New SMS ' . $form->userPhone,
                    'Message from ' . trim($form->user->full_name) . '. Text: '. $in->s_sms_text,
                    Notifications::TYPE_INFO,
                    true
                )) {
                    $dataNotification = (\Yii::$app->params['settings']['notification_web_socket']) ? NotificationMessage::add($ntf) : [];
                    Notifications::publish('getNewNotification', ['user_id' => $form->contactEntity->id], $dataNotification);
                } else {
                    Yii::error('Not created Sms notification to employee_id: ' . $form->contactEntity->id,'SmsSenderService:sendToInternalNumber');
                }
                Notifications::publish('phoneWidgetSmsSocketMessage', ['user_id' => $form->contactEntity->id], Message::add($in, $form->contactEntity, new Contact($form->user)));
            } else {
                $result['errors'] = $in->getErrors();
                $transaction->rollBack();
            }
        } else {
            $transaction->rollBack();
            $result['errors'] = $out->getErrors();
        }
        if ($result['success']) {
            $result['sms'] = (new SmsDto($out, $form->user, $form->getContact()))->toArray();
        }
        return $result;
    }

    public function sendToExternalNumber(SmsSendForm $form): array
    {
        $sms = new Sms();
        $sms->s_type_id = Sms::TYPE_OUTBOX;
        $sms->s_status_id = Sms::STATUS_PENDING;
        $sms->s_sms_text = $form->text;
        $sms->s_phone_from = $form->userPhone;
        $sms->s_phone_to = $form->getContactPhone();
        $sms->s_created_dt = date('Y-m-d H:i:s');
        $sms->s_created_user_id = $form->user->id;
        $sms->s_client_id = $form->getContactId();
        $sms->s_project_id = $form->getProjectId();

        $transaction = \Yii::$app->db->beginTransaction();
        if ($sms->save()) {
            $job = new SmsSendJob();
            $job->smsId = $sms->s_id;
            if ($jobId = Yii::$app->queue_sms_job->priority(100)->push($job)) {
                $transaction->commit();
                $result['success'] = true;
                $result['sms'] = (new SmsDto($sms, $form->user, new Contact($form->contactEntity)))->toArray();
            } else {
                $transaction->rollBack();
                $result['errors'] = ['job' => ['Cant create Job']];
            }
        } else {
            $transaction->rollBack();
            $result['errors'] = $sms->getErrors();
        }
        return $result;
    }
}
