<?php

use yii\db\Migration;

/**
 * Class m181217_082329_alter_user_params_add_frequency_minutes
 */
class m181217_082329_alter_user_params_add_frequency_minutes extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%user_params}}', 'up_frequency_minutes', $this->integer(4)->defaultValue(10));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%user_params}}', 'up_frequency_minutes');
    }

}
