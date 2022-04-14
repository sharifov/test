<?php

use yii\db\Migration;

/**
 * Class m220408_061602_add_ff_email_offer_ab_testing
 */
class m220408_061602_add_ff_email_offer_ab_testing extends Migration
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
            if (\kivork\FeatureFlag\Models\FeatureFlag::find()->where(['ff_key' =>  \modules\featureFlag\FFlag::FF_KEY_A_B_TESTING_EMAIL_OFFER_TEMPLATES])->exists()) {
                throw new \RuntimeException('FeatureFlag (' . \modules\featureFlag\FFlag::FF_KEY_A_B_TESTING_EMAIL_OFFER_TEMPLATES . ') already exist');
            }
            $featureFlagService = new \kivork\FeatureFlag\Services\FeatureFlagService();
            $featureFlagService::add(
                \modules\featureFlag\FFlag::FF_KEY_A_B_TESTING_EMAIL_OFFER_TEMPLATES,
                'Email offer templates AB testing',
                \kivork\FeatureFlag\Models\FeatureFlag::TYPE_ARRAY,
                json_encode([
                    'projects'         => [
                        'ovago' => [
                            'email_offer_test_type_1' => 50,
                            'email_offer_test_type_2' => 50,
                        ],
                    ],
                    'startingDateTime' => '2022-04-07 00:00:00',
                ], true),
                \kivork\FeatureFlag\Models\FeatureFlag::ET_DISABLED,
                [
                    'ff_category' => \modules\featureFlag\FFlag::FF_CATEGORY_A_B_TESTING,
                    'ff_description' => "Email offer templates AB testing.
                    [
                    'projects' => [
                        'ovago' => [
                            'email_offer_test_type_1' => 50
                            'email_offer_test_type_2' => 50
                        ]
                    ]
                    'startingDateTime' => '2022-04-07 00:00:00'
                    ]
                This is how the settings in the FF projects - list of projects look like, the array of projects stores the keys of email templates and each of them corresponds to the percentage of users to whom you need to send an email. the startingDateTime parameter corresponds to the testing start date
                also in the configuration it is possible to include all projects at once through '*'
                "
                ]
            );
            Yii::$app->ff->invalidateCache();
        } catch (\Throwable $throwable) {
            \Yii::error(\src\helpers\app\AppHelper::throwableLog($throwable), 'm220408_061602_add_ff_email_offer_ab_testing:safeUp:Throwable');
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
            $featureFlagService::delete(\modules\featureFlag\FFlag::FF_KEY_A_B_TESTING_EMAIL_OFFER_TEMPLATES);
            Yii::$app->ff->invalidateCache();
        } catch (\Throwable $throwable) {
            \Yii::error(\src\helpers\app\AppHelper::throwableLog($throwable), 'm220408_061602_add_ff_email_offer_ab_testing:safeDown:Throwable');
        }
    }
}
