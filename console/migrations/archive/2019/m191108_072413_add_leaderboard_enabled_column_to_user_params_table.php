<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%user_params}}`.
 */
class m191108_072413_add_leaderboard_enabled_column_to_user_params_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%user_params}}', 'up_leaderboard_enabled', $this->boolean()->defaultValue(1));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%user_params}}', 'up_leaderboard_enabled');
    }
}
