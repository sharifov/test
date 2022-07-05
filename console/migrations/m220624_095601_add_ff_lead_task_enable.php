<?php

use modules\featureFlag\FFlag;
use src\helpers\app\AppHelper;
use yii\db\Migration;

/**
 * Class m220624_095601_add_ff_lead_task_enable
 */
class m220624_095601_add_ff_lead_task_enable extends Migration
{
    private string $key = FFlag::FF_KEY_LEAD_TASK_ASSIGN;

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        try {
            if (!class_exists('\kivork\FeatureFlag\Models\FeatureFlag')) {
                throw new \RuntimeException('Class (FeatureFlag) not found');
            }
            if (\kivork\FeatureFlag\Models\FeatureFlag::find()->where(['ff_key' => $this->key])->exists()) {
                throw new \RuntimeException('FeatureFlag (' . $this->key . ') already exit');
            }
            $featureFlagService = new \kivork\FeatureFlag\Services\FeatureFlagService();
            $featureFlagService::add(
                $this->key,
                'Lead Task assign Enable',
                \kivork\FeatureFlag\Models\FeatureFlag::TYPE_BOOL,
                'true',
                \kivork\FeatureFlag\Models\FeatureFlag::ET_ENABLED,
                [
                    'ff_category' => FFlag::FF_CATEGORY_LEAD,
                    'ff_description' => 'Lead Task assign Enable/Disable'
                ]
            );
            Yii::$app->featureFlag->invalidateCache();
        } catch (\Throwable $throwable) {
            \Yii::error(AppHelper::throwableLog($throwable), 'm220624_095601_add_ff_lead_task_enable:safeUp:Throwable');
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
            $featureFlagService = new \kivork\FeatureFlag\Services\FeatureFlagService();
            if ($featureFlagService::delete($this->key)) {
                Yii::$app->featureFlag->invalidateCache();
            }
        } catch (\Throwable $throwable) {
            \Yii::error(AppHelper::throwableLog($throwable), 'm220624_095601_add_ff_lead_task_enable:safeDown:Throwable');
        }
    }
}
