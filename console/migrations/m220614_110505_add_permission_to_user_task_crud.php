<?php

use yii\db\Migration;

/**
 * Class m220614_110505_add_permission_to_user_task_crud
 */
class m220614_110505_add_permission_to_user_task_crud extends Migration
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
        echo "m220614_110505_add_permission_to_user_task_crud cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220614_110505_add_permission_to_user_task_crud cannot be reverted.\n";

        return false;
    }
    */
}
