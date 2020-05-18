<?php

use yii\db\Migration;

/**
 * Class m181024_074509_alter_user_params_add_shift_time
 */
class m181024_074509_alter_user_params_add_shift_time extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%user_params}}', 'up_work_start_tm', $this->time()->defaultValue('17:00:00'));
        $this->addColumn('{{%user_params}}', 'up_work_minutes', $this->integer()->defaultValue(480));
        $this->addColumn('{{%user_params}}', 'up_timezone', $this->string(40)->defaultValue('Europe/Chisinau'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%user_params}}', 'up_work_start_tm');
        $this->dropColumn('{{%user_params}}', 'up_work_minutes');
        $this->dropColumn('{{%user_params}}', 'up_timezone');
    }
}
