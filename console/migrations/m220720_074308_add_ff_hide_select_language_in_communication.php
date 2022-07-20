<?php

use kivork\FeatureFlag\Models\FeatureFlag;
use kivork\FeatureFlag\Services\FeatureFlagService;
use modules\featureFlag\FFlag;
use src\helpers\app\AppHelper;
use yii\db\Migration;

/**
 * Class m220720_074308_add_ff_hide_select_language_in_communication
 *
 * @description correction in m220720_105943_remove_ff_hide_select_language_in_communication
 */
class m220720_074308_add_ff_hide_select_language_in_communication extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
    }
}
