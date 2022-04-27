<?php

use src\helpers\app\AppHelper;
use src\model\leadPoorProcessingData\entity\LeadPoorProcessingDataDictionary;
use src\model\leadPoorProcessingData\entity\LeadPoorProcessingDataQuery;
use yii\db\Migration;

/**
 * Class m220216_065303_update_lppd_scheduled_communication
 */
class m220216_065303_update_lppd_scheduled_communication extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        try {
            if (!$rule = LeadPoorProcessingDataQuery::getRuleByKey(LeadPoorProcessingDataDictionary::KEY_SCHEDULED_COMMUNICATION)) {
                throw new \RuntimeException('Rule not found(' . LeadPoorProcessingDataDictionary::KEY_SCHEDULED_COMMUNICATION . ')');
            }
            $paramsJson = \frontend\helpers\JsonHelper::decode($rule->lppd_params_json);
            $excludeProjects = [
                'smsExcludeProjects' => ['priceline', 'kayak'],
            ];

            $rule->lppd_params_json = \yii\helpers\ArrayHelper::merge($paramsJson, $excludeProjects);

            if (!$rule->save()) {
                throw new \RuntimeException('Rule not saved');
            }
        } catch (\Throwable $throwable) {
            \Yii::error(AppHelper::throwableLog($throwable), 'm220216_065303_update_lppd_scheduled_communication:safeUp:Throwable');
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        try {
            if (!$rule = LeadPoorProcessingDataQuery::getRuleByKey(LeadPoorProcessingDataDictionary::KEY_SCHEDULED_COMMUNICATION)) {
                throw new \RuntimeException('Rule not found(' . LeadPoorProcessingDataDictionary::KEY_SCHEDULED_COMMUNICATION . ')');
            }
            $paramsJson = \frontend\helpers\JsonHelper::decode($rule->lppd_params_json);
            if (array_key_exists('smsExcludeProjects', $paramsJson)) {
                unset($paramsJson['smsExcludeProjects']);
                $rule->lppd_params_json = $paramsJson;
                if (!$rule->save()) {
                    throw new \RuntimeException('Rule not saved');
                }
            }
        } catch (\Throwable $throwable) {
            \Yii::error(AppHelper::throwableLog($throwable), 'm220216_065303_update_lppd_scheduled_communication:safeDown:Throwable');
        }
    }
}
