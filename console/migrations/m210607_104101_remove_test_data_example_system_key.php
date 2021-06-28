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
        $this->delete('{{%lead_data_key}}', ['IN', 'ldk_key', [
            'example_system_key'
        ]]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210607_104101_remove_test_data_example_system_key cannot be reverted.\n";
    }
}
