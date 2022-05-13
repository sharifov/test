<?php

use yii\db\Migration;

/**
 * Class m220511_082249_add_ff_ab_testing_offer_departments_config
 */
class m220511_082249_add_ff_ab_testing_offer_departments_config extends Migration
{
    /**
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        try {
            if (!class_exists('\kivork\FeatureFlag\Models\FeatureFlag')) {
                throw new \RuntimeException('Class (FeatureFlag) not found');
            }
            $fFlag = \kivork\FeatureFlag\Models\FeatureFlag::find()->where(['ff_key' =>  \modules\featureFlag\FFlag::FF_KEY_A_B_TESTING_EMAIL_OFFER_TEMPLATES])->one();
            if (empty($fFlag)) {
                return;
            }
            $value = json_decode($fFlag->ff_value, true);
            $value['departments'] = ['sales'];
            $fFlag->ff_value = json_encode($value);
            $fFlag->save();
            Yii::$app->ff->invalidateCache();
        } catch (\Throwable $throwable) {
            \Yii::error(\src\helpers\app\AppHelper::throwableLog($throwable), 'm220511_082249_add_ff_ab_testing_offer_departments_config:safeUp:Throwable');
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
            $fFlag = \kivork\FeatureFlag\Models\FeatureFlag::find()->where(['ff_key' =>  \modules\featureFlag\FFlag::FF_KEY_A_B_TESTING_EMAIL_OFFER_TEMPLATES])->one();
            if (empty($fFlag)) {
                return;
            }
            $value = json_decode($fFlag->ff_value, true);
            unset($value['departments']);
            $fFlag->ff_value = json_encode($value);
            $fFlag->save();
            Yii::$app->ff->invalidateCache();
        } catch (\Throwable $throwable) {
            \Yii::error(\src\helpers\app\AppHelper::throwableLog($throwable), 'm220511_082249_add_ff_ab_testing_offer_departments_config:safeDown:Throwable');
        }
    }
}
