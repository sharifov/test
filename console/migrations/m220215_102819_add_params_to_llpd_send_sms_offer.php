<?php

use src\helpers\app\AppHelper;
use src\model\leadPoorProcessingData\entity\LeadPoorProcessingDataDictionary;
use src\model\leadPoorProcessingData\entity\LeadPoorProcessingDataQuery;
use yii\db\Migration;

/**
 * Class m220215_102819_add_params_to_llpd_send_sms_offer
 */
class m220215_102819_add_params_to_llpd_send_sms_offer extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        try {
            if (!$rule = LeadPoorProcessingDataQuery::getRuleByKey(LeadPoorProcessingDataDictionary::KEY_SEND_SMS_OFFER)) {
                throw new \RuntimeException('Rule not found(' . LeadPoorProcessingDataDictionary::KEY_SEND_SMS_OFFER . ')');
            }
            $paramsJson = \frontend\helpers\JsonHelper::decode($rule->lppd_params_json);
            $excludeProjects = [
                'excludeProjects' => ['priceline', 'kayak'],
            ];

            $rule->lppd_params_json = \yii\helpers\ArrayHelper::merge($paramsJson, $excludeProjects);

            if (!$rule->save()) {
                throw new \RuntimeException('Rule not saved');
            }
        } catch (\Throwable $throwable) {
            \Yii::error(AppHelper::throwableLog($throwable, true), 'm220215_102819_add_params_to_llpd_send_sms_offer:safeUp:Throwable');
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        try {
            if (!$rule = LeadPoorProcessingDataQuery::getRuleByKey(LeadPoorProcessingDataDictionary::KEY_SEND_SMS_OFFER)) {
                throw new \RuntimeException('Rule not found(' . LeadPoorProcessingDataDictionary::KEY_SEND_SMS_OFFER . ')');
            }
            $paramsJson = \frontend\helpers\JsonHelper::decode($rule->lppd_params_json);
            if (array_key_exists('excludeProjects', $paramsJson)) {
                unset($paramsJson['excludeProjects']);
                $rule->lppd_params_json = $paramsJson;
                if (!$rule->save()) {
                    throw new \RuntimeException('Rule not saved');
                }
            }
        } catch (\Throwable $throwable) {
            \Yii::error(AppHelper::throwableLog($throwable, true), 'm220215_102819_add_params_to_llpd_send_sms_offer:safeDown:Throwable');
        }
    }
}
