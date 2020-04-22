<?php

use yii\db\Migration;

/**
 * Class m181015_133113_alter_user_params_add_bonus_turnoff
 */
class m181015_133113_alter_user_params_add_bonus_active extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%user_params}}', 'up_bonus_active', $this->boolean()->defaultValue(1));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%user_params}}', 'up_bonus_active');
    }
}
