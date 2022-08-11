<?php

use common\models\Setting;
use kivork\FeatureFlag\Models\FeatureFlag;
use kivork\FeatureFlag\Services\FeatureFlagService;
use modules\featureFlag\FFlag;
use src\helpers\app\AppHelper;
use yii\db\Migration;

/**
 * Class m220707_051817_add_site_setting_exclude_take_create_from_lead_user_conversion_by_source
 */
class m220707_051817_add_site_setting_exclude_take_create_from_lead_user_conversion_by_source extends Migration
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

            if (FeatureFlag::find()->where(['ff_key' =>  FFlag::FF_KEY_EXCLUDE_TAKE_CREATE_FROM_LEAD_USER_CONVERSION_BY_SOURCE_ENABLED])->exists()) {
                throw new \RuntimeException('FeatureFlag (' . FFlag::FF_KEY_EXCLUDE_TAKE_CREATE_FROM_LEAD_USER_CONVERSION_BY_SOURCE_ENABLED . ') already exist');
            }

            $featureFlagService = new FeatureFlagService();
            $featureFlagService::add(
                FFlag::FF_KEY_EXCLUDE_TAKE_CREATE_FROM_LEAD_USER_CONVERSION_BY_SOURCE_ENABLED,
                'Exclude take/create from lead user conversion by source',
                FeatureFlag::TYPE_BOOL,
                true,
                FeatureFlag::ET_DISABLED,
                [
                    'ff_category' => FFlag::FF_CATEGORY_LEAD,
                    'ff_description' => 'Exclude take/create from lead user conversion by source',
                ]
            );

            Yii::$app->featureFlag->invalidateCache();
        } catch (\Throwable $throwable) {
            Yii::error(AppHelper::throwableLog($throwable), 'm220707_051817_add_site_setting_exclude_take_create_from_lead_user_conversion_by_source:safeUp:Throwable');
        }

        $this->insert(
            '{{%setting}}',
            [
                's_key' => 'exclude_take_create_from_lead_user_conversion_by_source',
                's_name' => 'Exclude take/create from lead user conversion by source',
                's_type' => Setting::TYPE_ARRAY,
                's_value' => json_encode([
                    'ATZDCV', 'ATZMCV', 'AFBADS', 'AGFCBA',
                ]),
                's_updated_dt' => date('Y-m-d H:i:s'),
            ]
        );

        Yii::$app->db->getSchema()->refreshTableSchema('{{%setting}}');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->delete('{{%setting}}', ['IN', 's_key', [
            'exclude_take_create_from_lead_user_conversion_by_source',
        ]]);

        Yii::$app->db->getSchema()->refreshTableSchema('{{%setting}}');

        try {
            if (!class_exists('\kivork\FeatureFlag\Models\FeatureFlag')) {
                throw new \RuntimeException('Class (FeatureFlag) not found');
            }

            $featureFlagService = new FeatureFlagService();
            $featureFlagService::delete(FFlag::FF_KEY_EXCLUDE_TAKE_CREATE_FROM_LEAD_USER_CONVERSION_BY_SOURCE_ENABLED);

            Yii::$app->featureFlag->invalidateCache();
        } catch (\Throwable $throwable) {
            Yii::error(AppHelper::throwableLog($throwable), 'm220707_051817_add_site_setting_exclude_take_create_from_lead_user_conversion_by_source:safeDown:Throwable');
        }
    }
}
