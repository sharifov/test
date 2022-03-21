<?php

use modules\featureFlag\FFlag;
use src\helpers\app\AppHelper;
use yii\db\Migration;

/**
 * Class m220316_105146_add_ff_auto_add_quotes
 */
class m220316_105146_add_ff_auto_add_quotes extends Migration
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

            if (!\kivork\FeatureFlag\Models\FeatureFlag::find()->where(['ff_key' => FFlag::FF_KEY_ADD_AUTO_QUOTES])->exists()) {
                $featureFlagService = new \kivork\FeatureFlag\Services\FeatureFlagService();
                $featureFlagService::add(
                    FFlag::FF_KEY_ADD_AUTO_QUOTES,
                    'Auto add quotes',
                    \kivork\FeatureFlag\Models\FeatureFlag::TYPE_INT,
                    '5',
                    \kivork\FeatureFlag\Models\FeatureFlag::ET_DISABLED,
                    [
                        'ff_category' => FFlag::FF_CATEGORY_LEAD,
                        'ff_description' => 'Auto add quotes in create Flight Request processing'
                    ]
                );
                Yii::$app->ff->invalidateCache();
            }
        } catch (\Throwable $throwable) {
            \Yii::error(AppHelper::throwableLog($throwable), 'm220316_105146_add_ff_auto_add_quotes:safeUp:Throwable');
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
            $featureFlagService::delete(FFlag::FF_KEY_ADD_AUTO_QUOTES);
            Yii::$app->ff->invalidateCache();
        } catch (\Throwable $throwable) {
            \Yii::error(AppHelper::throwableLog($throwable), 'm220316_105146_add_ff_auto_add_quotes:safeDown:Throwable');
        }
    }
}
