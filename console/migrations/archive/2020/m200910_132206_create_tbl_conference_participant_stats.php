<?php

use yii\db\Migration;

/**
 * Class m200910_132206_create_tbl_conference_participant_stats
 */
class m200910_132206_create_tbl_conference_participant_stats extends Migration
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

        $this->createTable('{{%conference_participant_stats}}', [
            'cps_id' => $this->primaryKey(),
            'cps_cf_id' => $this->integer(),
            'cps_cf_sid' => $this->string(34),
            'cps_participant_identity' => $this->string(50),
            'cps_user_id' => $this->integer(),
            'cps_created_dt' => $this->dateTime()->notNull(),
            'cps_duration' => $this->smallInteger(6),
            'cps_talk_time' => $this->smallInteger(6),
            'cps_hold_time' => $this->smallInteger(6),
        ], $tableOptions);

        $this->createIndex('IND-cps-cf_sid-identity', '{{%conference_participant_stats}}', ['cps_cf_sid', 'cps_participant_identity'], true);
        $this->addForeignKey('FK-cps-cps_user_id', '{{%conference_participant_stats}}', ['cps_user_id'], '{{%employees}}', ['id'], 'SET NULL', 'CASCADE');
        $this->addForeignKey('FK-cps-cps_cf_id', '{{%conference_participant_stats}}', ['cps_cf_id'], '{{%conference}}', ['cf_id'], 'SET NULL', 'CASCADE');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('FK-cps-cps_cf_id', '{{%conference_participant_stats}}');
        $this->dropForeignKey('FK-cps-cps_user_id', '{{%conference_participant_stats}}');
        $this->dropTable('{{%conference_participant_stats}}');
    }
}
