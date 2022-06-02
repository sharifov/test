<?php

/**
 * Created by PhpStorm.
 * User: shakarim
 * Date: 5/17/22
 * Time: 10:00 AM
 */

namespace common\components;

use frostealth\yii2\aws\s3\Service;
use yii\base\Component;
use yii\swiftmailer\Mailer;

class ApplicationStatus extends Component
{
    /**
     * Creates metric string by received args
     *
     * Using example:
     *
     * ```php
     *
     *      ...
     *      echo \Yii::$app->applicationStatus::create("myMetric", ['some_param_1' => 'ValueNo1', 'some_param_2' => 'ValueNo2'], 3.14)
     *      ...
     * ```
     *
     * The result of this code will be:
     *
     *      myMetric{some_param_1="ValueNo1", some_param_2="ValueNo2"} 3.14
     *
     *
     * @param string $metricName
     * @param array $labels
     * @param $value
     * @return string
     */
    public static function create(string $metricName, array $labels, $value): string
    {
        $labels = array_reduce(array_keys($labels), function ($acc, $key) use ($labels) {
            $acc[] = "{$key}=\"{$labels[$key]}\"";
            return $acc;
        }, []);
        $labelsString = implode(',', $labels);
        return "{$metricName}{{$labelsString}} {$value}";
    }

    /**
     * @return string
     */
    public function dbStatus(): string
    {
        try {
            return \Yii::$app->db->isActive ? 'ok' : 'error';
        } catch (\Throwable $e) {
            return 'error';
        }
    }

    /**
     * @return string
     */
    public function dbSlaveStatus(): string
    {
        try {
            return \Yii::$app->db_slave->isActive ? 'ok' : 'error';
        } catch (\Throwable $e) {
            return 'error';
        }
    }

    /**
     * @return string
     */
    public function dbPostgresStatus(): string
    {
        try {
            return \Yii::$app->db_postgres->isActive ? 'ok' : 'error';
        } catch (\Throwable $e) {
            return 'error';
        }
    }

    /**
     * @return string
     */
    public function redisStatus(): string
    {
        try {
            return \Yii::$app->redis->isActive ? 'ok' : 'error';
        } catch (\Throwable $e) {
            return 'error';
        }
    }

    /**
     * @return string
     */
    public function mailerStatus(): string
    {
        try {
            /** @var Mailer $mailer */
            $mailer = \Yii::$app->mailer;
            return $mailer->transport->ping()  ? 'ok' : 'error';
        } catch (\Throwable $e) {
            return 'error';
        }
    }

    /**
     * @return string
     */
    public function communicationStatus(): string
    {
        try {
            return \Yii::$app->communication->ping() ? 'ok' : 'error';
        } catch (\Throwable $e) {
            return 'error';
        }
    }

    /**
     * @return string
     */
    public function airSearchStatus(): string
    {
        try {
            return \Yii::$app->airsearch->ping() ? 'ok' : 'error';
        } catch (\Throwable $e) {
            return 'error';
        }
    }

    /**
     * @return string
     */
    public function rChatStatus(): string
    {
        try {
            return \Yii::$app->rchat->ping() ? 'ok' : 'error';
        } catch (\Throwable $e) {
            return 'error';
        }
    }

    /**
     * @return string
     */
    public function chatBotStatus(): string
    {
        try {
            return \Yii::$app->chatBot->ping() ? 'ok' : 'error';
        } catch (\Throwable $e) {
            return 'error';
        }
    }

    /**
     * @return string
     */
    public function travelServicesStatus(): string
    {
        try {
            return \Yii::$app->travelServices->ping() ? 'ok' : 'error';
        } catch (\Throwable $e) {
            return 'error';
        }
    }

    /**
     * @return string
     */
    public function queueSmsJobStatus(): string
    {
        try {
            \Yii::$app->queue_sms_job->getStatusTube();
        } catch (\Throwable $e) {
            return 'error';
        }
    }

    /**
     * @return string
     */
    public function queueEmailJobStatus(): string
    {
        try {
            \Yii::$app->queue_email_job->getStatusTube();
        } catch (\Throwable $e) {
            return 'error';
        }
    }

    /**
     * @return string
     */
    public function queuePhoneCheckStatus(): string
    {
        try {
            \Yii::$app->queue_phone_check->getStatusTube();
        } catch (\Throwable $e) {
            return 'error';
        }
    }

    /**
     * @return string
     */
    public function queueJobStatus(): string
    {
        try {
            \Yii::$app->queue_job->getStatusTube();
        } catch (\Throwable $e) {
            return 'error';
        }
    }

    /**
     * @return string
     */
    public function queueSystemServicesStatus(): string
    {
        try {
            \Yii::$app->queue_system_services->getStatusTube();
        } catch (\Throwable $e) {
            return 'error';
        }
    }

    /**
     * @return string
     */
    public function queueClientChatJobStatus(): string
    {
        try {
            \Yii::$app->queue_client_chat_job->getStatusTube();
        } catch (\Throwable $e) {
            return 'error';
        }
    }

    /**
     * @return string
     */
    public function queueVirtualCronStatus(): string
    {
        try {
            \Yii::$app->queue_virtual_cron->getStatusTube();
        } catch (\Throwable $e) {
            return 'error';
        }
    }

    /**
     * @return string
     */
    public function queueLeadRedialStatus(): string
    {
        try {
            \Yii::$app->queue_lead_redial->getStatusTube();
        } catch (\Throwable $e) {
            return 'error';
        }
    }

    /**
     * @return string
     */
    public function telegramStatus(): string
    {
        try {
            $response = \Yii::$app->telegram->getMe();
            $result = $response->getResult();
            return isset($result['user']) ? 'ok' : 'error';
        } catch (\Throwable $e) {
            return 'error';
        }
    }

    /**
     * @return string
     */
    public function gaRequestServiceStatus(): string
    {
        try {
            return \Yii::$app->gaRequestService->ping() ? 'ok' : 'error';
        } catch (\Throwable $e) {
            return 'error';
        }
    }

    /**
     * @return string
     */
    public function centrifugoStatus(): string
    {
        try {
            \Yii::$app->centrifugo->getClient()->info();
            return 'ok';
        } catch (\Throwable $e) {
            return 'error';
        }
    }

    /**
     * @return string
     */
    public function callAntiSpamStatus(): string
    {
        try {
            return \Yii::$app->callAntiSpam->ping() ? 'ok' : 'error';
        } catch (\Throwable $e) {
            return 'error';
        }
    }
}
