<?php

use yii\db\Migration;

/**
 * Class m220225_124128_create_tbl_lead_status_reason
 */
class m220225_124128_create_tbl_lead_status_reason extends Migration
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

        $this->createTable('{{%lead_status_reason}}', [
            'lsr_id' => $this->primaryKey(),
            'lsr_key' => $this->string(30)->unique(),
            'lsr_name' => $this->string(50),
            'lsr_description' => $this->string(),
            'lsr_enabled' => $this->boolean(),
            'lsr_comment_required' => $this->boolean(),
            'lsr_params' => $this->json(),
            'lsr_created_user_id' => $this->integer(),
            'lsr_updated_user_id' => $this->integer(),
            'lsr_created_dt' => $this->timestamp(),
            'lsr_updated_dt' => $this->timestamp()
        ], $tableOptions);

        $this->addForeignKey(
            'FK-lead_status_reason-lsr_created_user_id',
            '{{%lead_status_reason}}',
            'lsr_created_user_id',
            '{{%employees}}',
            'id',
            'SET NULL',
            'CASCADE'
        );

        $this->addForeignKey(
            'FK-lead_status_reason-lsr_updated_user_id',
            '{{%lead_status_reason}}',
            'lsr_updated_user_id',
            '{{%employees}}',
            'id',
            'SET NULL',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%lead_status_reason}}');
    }
}
