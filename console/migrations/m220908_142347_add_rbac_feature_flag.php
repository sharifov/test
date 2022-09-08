<?php

use kivork\FeatureFlag\Services\FeatureFlagService;
use yii\db\Migration;

/**
 * Class m220908_142347_add_rbac_feature_flag
 */
class m220908_142347_add_rbac_feature_flag extends Migration
{
    private const API_RBAC = 'boApiRbacAuth';

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $ff = new FeatureFlagService();
        $ff->addSimpleFlag(
            self::API_RBAC,
            false,
            'API RBAC authorization',
            'rbac',
            'Require API User authorization',
        );

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $ff = new FeatureFlagService();
        $ff->delete(self::API_RBAC);

        return true;
    }
}
