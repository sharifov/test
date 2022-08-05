<?php

use yii\db\Migration;

/**
 * Class m220804_104352_add_column_user_task_description
 */
class m220804_104352_add_column_user_task_description extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn(
            '{{%user_task}}',
            'ut_description',
            $this->string(255)
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%user_task}}', 'ut_description');
    }
}
