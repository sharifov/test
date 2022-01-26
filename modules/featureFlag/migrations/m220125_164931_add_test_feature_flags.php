<?php

namespace modules\featureFlag\migrations;

use modules\featureFlag\src\entities\FeatureFlag;
use modules\featureFlag\src\FeatureFlagService;
use Yii;
use yii\db\Migration;

/**
 * Handles the creation of table `{{%feature_flag}}`.
 */
class m220125_164931_add_test_feature_flags extends Migration
{
    public const FF1 = 'testFlag1';
    public const FF2 = 'testFlag2';

    /**
     * @throws \yii\base\Exception
     */
    public function safeUp()
    {
        $ff = new FeatureFlagService();
        $ff->add(self::FF1, 'Test Flag 1', FeatureFlag::TYPE_BOOL, 'true', FeatureFlag::ET_ENABLED);
        $ff->add(
            self::FF2,
            'Test Flag 2',
            FeatureFlag::TYPE_ARRAY,
            json_encode(['a' => 1, 'b' => 3.25, 'c' => 'test']),
            FeatureFlag::ET_ENABLED
        );


        Yii::$app->ff->invalidateCache();
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $ff = new FeatureFlagService();
        $ff->delete(self::FF1);
        $ff->delete(self::FF2);
    }
}
