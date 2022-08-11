<?php

use yii\db\Migration;

/**
 * Class m220711_061113_alter_table_client_data_remove_unique_key
 */
class m220711_061113_alter_table_client_data_remove_unique_key extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->dropForeignKey('FK-client_data-key', '{{%client_data}}');
        $this->dropForeignKey('FK-client_data-client', '{{%client_data}}');
        $this->dropIndex('IND-client_data-client_id-field_id', '{{%client_data}}');
        $this->addForeignKey(
            'FK-client_data-key',
            '{{%client_data}}',
            ['cd_key_id'],
            '{{%client_data_key}}',
            'cdk_id',
            'SET NULL',
            'CASCADE'
        );
        $this->addForeignKey(
            'FK-client_data-client',
            '{{%client_data}}',
            ['cd_client_id'],
            '{{%clients}}',
            'id',
            'SET NULL',
            'CASCADE'
        );
        $this->createIndex('IND-client_data-client_id-field_id', '{{%client_data}}', ['cd_client_id', 'cd_key_id']);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('FK-client_data-key', '{{%client_data}}');
        $this->dropForeignKey('FK-client_data-client', '{{%client_data}}');
        $this->dropIndex('IND-client_data-client_id-field_id', '{{%client_data}}');
        $this->addForeignKey(
            'FK-client_data-key',
            '{{%client_data}}',
            ['cd_key_id'],
            '{{%client_data_key}}',
            'cdk_id',
            'SET NULL',
            'CASCADE'
        );
        $this->addForeignKey(
            'FK-client_data-client',
            '{{%client_data}}',
            ['cd_client_id'],
            '{{%clients}}',
            'id',
            'SET NULL',
            'CASCADE'
        );
        $this->createIndex('IND-client_data-client_id-field_id', '{{%client_data}}', ['cd_client_id', 'cd_key_id'], true);
    }
}
