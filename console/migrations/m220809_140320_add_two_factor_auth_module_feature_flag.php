<?php

use yii\db\Migration;
use kivork\FeatureFlag\Services\FeatureFlagService;

/**
 * Class m220809_140320_add_two_factor_auth_module_feature_flag
 */
class m220809_140320_add_two_factor_auth_module_feature_flag extends Migration
{
    const TWO_FACTOR_AUTH_MODULE = 'two-factor-auth-module';

    /**
     * @return bool
     * @throws \yii\base\Exception
     */
    public function safeUp(): bool
    {
        $ff = new FeatureFlagService();
        $ff->addSimpleFlag(
            self::TWO_FACTOR_AUTH_MODULE,
            false,
            'Two Factor Auth Module',
            'two-factor-auth',
            'New logic for two factor auth'
        );

        return true;
    }

    /**
     * {@inheritdoc}
     * @return bool
     */
    public function safeDown(): bool
    {
        $ff = new FeatureFlagService();
        $ff->delete(self::TWO_FACTOR_AUTH_MODULE);

        return true;
    }
}
