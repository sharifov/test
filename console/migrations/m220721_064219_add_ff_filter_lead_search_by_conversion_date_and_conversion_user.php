<?php

use kivork\FeatureFlag\Models\FeatureFlag;
use kivork\FeatureFlag\Services\FeatureFlagService;
use modules\featureFlag\FFlag;
use src\helpers\app\AppHelper;
use yii\db\Migration;

/**
 * Class m220721_064219_add_ff_filter_lead_search_by_conversion_date_and_conversion_user
 */
class m220721_064219_add_ff_filter_lead_search_by_conversion_date_and_conversion_user extends Migration
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

            if (FeatureFlag::find()->where(['ff_key' =>  FFlag::FF_KEY_FILTER_CONVERSION_DATE_AND_USER_IN_LEAD_SEARCH])->exists()) {
                throw new \RuntimeException('FeatureFlag (' . FFlag::FF_KEY_FILTER_CONVERSION_DATE_AND_USER_IN_LEAD_SEARCH . ') already exist');
            }

            $featureFlagService = new FeatureFlagService();
            $featureFlagService::add(
                FFlag::FF_KEY_FILTER_CONVERSION_DATE_AND_USER_IN_LEAD_SEARCH,
                'Filter Conversion Date and User In LeadSearch Enable',
                FeatureFlag::TYPE_BOOL,
                true,
                FeatureFlag::ET_DISABLED,
                [
                    'ff_category' => FFlag::FF_KEY_FILTER_CONVERSION_DATE_AND_USER_IN_LEAD_SEARCH,
                    'ff_description' => 'Filter Conversion Date and User In LeadSearch Enable',
                ]
            );

            Yii::$app->featureFlag->invalidateCache();
        } catch (\Throwable $throwable) {
            Yii::error(AppHelper::throwableLog($throwable), 'm220721_064219_add_ff_filter_lead_search_by_conversion_date_and_conversion_user:safeUp:Throwable');
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
            $featureFlagService::delete(FFlag::FF_KEY_FILTER_CONVERSION_DATE_AND_USER_IN_LEAD_SEARCH);

            Yii::$app->featureFlag->invalidateCache();
        } catch (\Throwable $throwable) {
            Yii::error(AppHelper::throwableLog($throwable), 'm220721_064219_add_ff_filter_lead_search_by_conversion_date_and_conversion_user:safeDown:Throwable');
        }
    }
}
