<?php

use kivork\FeatureFlag\Models\FeatureFlag;
use kivork\FeatureFlag\Services\FeatureFlagService;
use modules\featureFlag\FFlag;
use src\helpers\app\AppHelper;
use yii\db\Migration;

/**
 * Class m220802_101743_add_ff_lead_task_list_history_modal_enable
 */
class m220802_101743_add_ff_lead_task_list_history_modal_enable extends Migration
{
    public function safeUp()
    {
        try {
            if (!class_exists('\kivork\FeatureFlag\Models\FeatureFlag')) {
                throw new \RuntimeException('Class (FeatureFlag) not found');
            }

            if (FeatureFlag::find()->where(['ff_key' =>  FFlag::FF_KEY_LEAD_TASK_LIST_HISTORY_MODAL_ENABLE])->exists()) {
                throw new \RuntimeException('FeatureFlag (' . FFlag::FF_KEY_LEAD_TASK_LIST_HISTORY_MODAL_ENABLE . ') already exist');
            }

            $featureFlagService = new FeatureFlagService();
            $featureFlagService::add(
                FFlag::FF_KEY_LEAD_TASK_LIST_HISTORY_MODAL_ENABLE,
                'Enable modal in lead view with history of task',
                FeatureFlag::TYPE_BOOL,
                true,
                FeatureFlag::ET_DISABLED,
                [
                    'ff_category' => FFlag::FF_CATEGORY_LEAD,
                    'ff_description' => 'Enable modal in lead view with history of task',
                ]
            );

            Yii::$app->featureFlag->invalidateCache();
        } catch (\Throwable $throwable) {
            Yii::error(AppHelper::throwableLog($throwable), 'm220802_101743_add_ff_lead_task_list_history_modal_enable:safeUp:Throwable');
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        try {
            if (!class_exists('\kivork\FeatureFlag\Models\FeatureFlag')) {
                throw new \RuntimeException('Class (FeatureFlag) not found');
            }

            $featureFlagService = new FeatureFlagService();
            $featureFlagService::delete(FFlag::FF_KEY_LEAD_TASK_LIST_HISTORY_MODAL_ENABLE);

            Yii::$app->featureFlag->invalidateCache();
        } catch (\Throwable $throwable) {
            Yii::error(AppHelper::throwableLog($throwable), 'm220802_101743_add_ff_lead_task_list_history_modal_enable:safeDown:Throwable');
        }
    }
}
