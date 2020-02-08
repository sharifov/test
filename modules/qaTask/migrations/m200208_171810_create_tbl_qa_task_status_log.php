<?php

use yii\db\Migration;

/**
 * Class m200208_171810_create_tbl_qa_task_status_log
 */
class m200208_171810_create_tbl_qa_task_status_log extends Migration
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
        echo "m200208_171810_create_tbl_qa_task_status_log cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200208_171810_create_tbl_qa_task_status_log cannot be reverted.\n";

        return false;
    }
    */
}
