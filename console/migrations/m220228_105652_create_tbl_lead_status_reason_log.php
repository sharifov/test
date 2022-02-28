<?php

use yii\db\Migration;

/**
 * Class m220228_105652_create_tbl_lead_status_reason_log
 */
class m220228_105652_create_tbl_lead_status_reason_log extends Migration
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

        $this->createTable('{{%lead_status_reason_log}}', [
            'lsrl_id' => $this->primaryKey(),
            'lsrl_lead_flow_id' => $this->integer(),
            'lsrl_lead_status_reason_id' => $this->integer(),
            'lsrl_comment' => $this->string(),
            'lsrl_created_dt' => $this->dateTime()
        ], $tableOptions);

        $this->addForeignKey('FK-lead_status_reason_log-lsrl_lead_flow_id', '{{%lead_status_reason_log}}', 'lsrl_lead_flow_id', '{{%lead_flow}}', 'id', 'SET NULL', 'CASCADE');
        $this->addForeignKey('FK-lead_status_reason_log-lsrl_lead_status_reason_id', '{{%lead_status_reason_log}}', 'lsrl_lead_status_reason_id', '{{%lead_status_reason}}', 'lsr_id', 'SET NULL', 'CASCADE');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%lead_status_reason_log}}');
    }
}
