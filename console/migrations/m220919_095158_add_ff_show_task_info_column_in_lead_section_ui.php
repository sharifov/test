<?php

use yii\db\Migration;
use src\helpers\app\AppHelper;

/**
 * Class m220919_095158_add_ff_show_task_info_column_in_lead_section_ui
 */
class m220919_095158_add_ff_show_task_info_column_in_lead_section_ui extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        try {
            if (!class_exists('\kivork\FeatureFlag\Models\FeatureFlag')) {
                throw new \RuntimeException('Class (FeatureFlag) not found');
            }
            if (\kivork\FeatureFlag\Models\FeatureFlag::find()->where(['ff_key' => \modules\featureFlag\FFlag::FF_KEY_SHOW_TASK_INFO_COLUMN_ON_LEAD_SECTION_UI])->exists()) {
                throw new \RuntimeException('FeatureFlag (' . \modules\featureFlag\FFlag::FF_KEY_SHOW_TASK_INFO_COLUMN_ON_LEAD_SECTION_UI . ') already exit');
            }

            $featureFlagService = new \kivork\FeatureFlag\Services\FeatureFlagService();
            $featureFlagService::add(
                \modules\featureFlag\FFlag::FF_KEY_SHOW_TASK_INFO_COLUMN_ON_LEAD_SECTION_UI,
                'Show "Task info" column from Lead section on UI',
                \kivork\FeatureFlag\Models\FeatureFlag::TYPE_BOOL,
                true,
                \kivork\FeatureFlag\Models\FeatureFlag::ET_ENABLED,
                [
                    'ff_category' => \modules\featureFlag\FFlag::FF_CATEGORY_LEAD,
                    'ff_description' => 'Show "Task info" column from Lead section on UI',
                ]
            );

            \Yii::$app->featureFlag->invalidateCache();
        } catch (\Throwable $throwable) {
            \Yii::error(
                AppHelper::throwableLog($throwable),
                'm220919_095158_add_ff_show_task_info_column_in_lead_section_ui:safeUp:Throwable'
            );
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
            $featureFlagService::delete(\modules\featureFlag\FFlag::FF_KEY_SHOW_TASK_INFO_COLUMN_ON_LEAD_SECTION_UI);

            \Yii::$app->featureFlag->invalidateCache();
        } catch (\Throwable $throwable) {
            \Yii::error(
                AppHelper::throwableLog($throwable),
                'm220919_095158_add_ff_show_task_info_column_in_lead_section_ui:safeDown:Throwable'
            );
        }
    }
}
