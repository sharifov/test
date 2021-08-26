<?php

namespace sales\model\project\entity\params;

/**
 * Class ClientNotificationObject
 *
 * @property SendPhoneNotification $sendPhoneNotification
 * @property SendSmsNotification $sendSmsNotification
 */
class ClientNotificationObject
{
    public SendPhoneNotification $sendPhoneNotification;
    public SendSmsNotification $sendSmsNotification;

    public function __construct(array $params)
    {
        if (array_key_exists('sendPhoneNotification', $params) && is_array($params['sendPhoneNotification'])) {
            $this->sendPhoneNotification = new SendPhoneNotification($params['sendPhoneNotification']);
        } else {
            $this->sendPhoneNotification = new SendPhoneNotification([]);
        }

        if (array_key_exists('sendSmsNotification', $params) && is_array($params['sendSmsNotification'])) {
            $this->sendSmsNotification = new SendSmsNotification($params['sendSmsNotification']);
        } else {
            $this->sendSmsNotification = new SendSmsNotification([]);
        }
    }
}
