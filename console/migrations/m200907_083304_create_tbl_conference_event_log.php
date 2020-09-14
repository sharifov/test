<?php

use yii\db\Migration;

/**
 * Class m200907_083304_create_tbl_conference_event_log
 */
class m200907_083304_create_tbl_conference_event_log extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%conference_event_log}}', [
            'cel_id' => $this->primaryKey(),
            'cel_event_type' => $this->string(50)->notNull(),
            'cel_conference_sid' => $this->string(34)->notNull(),
            'cel_sequence_number' => $this->smallInteger(),
            'cel_created_dt' => $this->dateTime()->notNull(),
            'cel_data' => $this->text()->notNull(),
        ], $tableOptions);

        $this->createIndex('IND-conference_event_log-cel_conference_sid', '{{%conference_event_log}}', ['cel_conference_sid']);
        $this->createIndex('IND-conference_event_log-cel_sequence_number', '{{%conference_event_log}}', ['cel_sequence_number']);
        $this->createIndex('IND-conference_event_log-cel_created_dt', '{{%conference_event_log}}', ['cel_created_dt']);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropIndex('IND-conference_event_log-cel_created_dt', '{{%conference_event_log}}');
        $this->dropIndex('IND-conference_event_log-cel_sequence_number', '{{%conference_event_log}}');
        $this->dropIndex('IND-conference_event_log-cel_conference_sid', '{{%conference_event_log}}');
        $this->dropTable('{{%conference_event_log}}');
    }
}
