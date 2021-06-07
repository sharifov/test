<?php

use yii\db\Migration;

/**
 * Class m210607_104101_remove_test_data_example_system_key
 */
class m210607_104101_remove_test_data_example_system_key extends Migration
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
        echo "m210607_104101_remove_test_data_example_system_key cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210607_104101_remove_test_data_example_system_key cannot be reverted.\n";

        return false;
    }
    */
}
