<?php

use kivork\FeatureFlag\Models\FeatureFlag;
use kivork\FeatureFlag\Services\FeatureFlagService;
use modules\featureFlag\FFlag;
use src\helpers\app\AppHelper;
use yii\db\Migration;

/**
 * Class m220728_081423_add_ff_booked_queue_agent_is_owner
 */
class m220728_081423_add_ff_booked_queue_agent_is_owner extends Migration
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

            if (FeatureFlag::find()->where(['ff_key' =>  FFlag::FF_KEY_BOOKED_QUEUE_CONDITION_AGENT_IS_OWNER])->exists()) {
                throw new \RuntimeException('FeatureFlag (' . FFlag::FF_KEY_BOOKED_QUEUE_CONDITION_AGENT_IS_OWNER . ') already exist');
            }

            $featureFlagService = new FeatureFlagService();
            $featureFlagService::add(
                FFlag::FF_KEY_BOOKED_QUEUE_CONDITION_AGENT_IS_OWNER,
                'Booked Queue condition for agent only owner',
                FeatureFlag::TYPE_BOOL,
                true,
                FeatureFlag::ET_DISABLED,
                [
                    'ff_category' => FFlag::FF_KEY_BOOKED_QUEUE_CONDITION_AGENT_IS_OWNER,
                    'ff_description' => 'Booked Queue condition for agent only owner',
                ]
            );

            Yii::$app->featureFlag->invalidateCache();
        } catch (\Throwable $throwable) {
            Yii::error(AppHelper::throwableLog($throwable), 'm220728_081423_add_ff_booked_queue_agent_is_owner:safeUp:Throwable');
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
            $featureFlagService::delete(FFlag::FF_KEY_BOOKED_QUEUE_CONDITION_AGENT_IS_OWNER);

            Yii::$app->featureFlag->invalidateCache();
        } catch (\Throwable $throwable) {
            Yii::error(AppHelper::throwableLog($throwable), 'm220728_081423_add_ff_booked_queue_agent_is_owner:safeDown:Throwable');
        }
    }
}
