<?php

namespace src\model\project\entity\params;

/**
 * Class ClientNotificationObject
 *
 * @property SendPhoneNotification $sendPhoneNotification
 * @property SendSmsNotification $sendSmsNotification
 * @property SendEmailNotification $sendEmailNotification
 */
class ClientNotificationObject
{
    public SendPhoneNotification $sendPhoneNotification;
    public SendSmsNotification $sendSmsNotification;
    public SendEmailNotification $sendEmailNotification;

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

        if (array_key_exists('sendEmailNotification', $params) && is_array($params['sendEmailNotification'])) {
            $this->sendEmailNotification = new SendEmailNotification($params['sendEmailNotification']);
        } else {
            $this->sendEmailNotification = new SendEmailNotification([]);
        }
    }

    public function isAnyEnabled(): bool
    {
        return $this->sendPhoneNotification->enabled || $this->sendSmsNotification->enabled || $this->sendEmailNotification->enabled;
    }
}
