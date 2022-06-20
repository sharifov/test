<?php

use yii\db\Migration;

/**
 * Class m220427_081640_add_ff_ab_testing_offer_templates_default_template_key_config
 */
class m220427_081640_add_ff_ab_testing_offer_templates_default_template_key_config extends Migration
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
            $value['defaultOfferTemplateKey'] = 'cl_offer';
            $fFlag->ff_value = json_encode($value);
            $fFlag->save();
            Yii::$app->featureFlag->invalidateCache();
        } catch (\Throwable $throwable) {
            \Yii::error(\src\helpers\app\AppHelper::throwableLog($throwable), 'm220427_081640_add_ff_ab_testing_offer_templates_default_template_key_config:safeUp:Throwable');
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
            unset($value['defaultOfferTemplateKey']);
            $fFlag->ff_value = json_encode($value);
            $fFlag->save();
            Yii::$app->featureFlag->invalidateCache();
        } catch (\Throwable $throwable) {
            \Yii::error(\src\helpers\app\AppHelper::throwableLog($throwable), 'm220427_081640_add_ff_ab_testing_offer_templates_default_template_key_config:safeDown:Throwable');
        }
    }
}
