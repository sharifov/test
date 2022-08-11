<?php

use yii\db\Migration;

/**
 * Class m220629_131406_user_params_set_take_frequency_minutes
 */
class m220629_131406_user_params_set_take_frequency_minutes extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->update('{{%user_params}}', ['up_frequency_minutes' => 10]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220629_131406_user_params_set_take_frequency_minutes cannot be reverted.\n";

        return true;
    }
}
