<?php

use yii\db\Migration;

/**
 * Class m210121_084517_add_permission_file_view
 */
class m210121_084517_add_permission_file_view extends Migration
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
        echo "m210121_084517_add_permission_file_view cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210121_084517_add_permission_file_view cannot be reverted.\n";

        return false;
    }
    */
}
