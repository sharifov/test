<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%user_group}}`.
 */
class m191211_074109_add_on_leaderboard_column_to_user_group_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%user_group}}', 'ug_on_leaderboard', $this->boolean()->defaultValue(0));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%user_params}}', 'ug_on_leaderboard');
    }
}
