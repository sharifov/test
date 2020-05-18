<?php

use yii\db\Migration;

/**
 * Class m190502_120134_create_tbl_lead_call_expert
 */
class m190502_120134_create_tbl_lead_call_expert extends Migration
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


        $this->createTable('{{%lead_call_expert}}', [
            'lce_id'                => $this->primaryKey(),
            'lce_lead_id'           => $this->integer()->notNull(),
            'lce_request_text'      => $this->text()->notNull(),
            'lce_request_dt'        => $this->dateTime(),
            'lce_response_text'     => $this->text(),
            'lce_response_lead_quotes'  => $this->text(),
            'lce_response_dt'       => $this->dateTime(),
            'lce_status_id'         => $this->tinyInteger(1),
            'lce_agent_user_id'     => $this->integer(),
            'lce_expert_user_id'    => $this->integer(),
            'lce_expert_username'   => $this->string(30),
            'lce_updated_dt'        => $this->dateTime(),
        ], $tableOptions);

        $this->addForeignKey('FK-lead_call_expert_lce_lead_id', '{{%lead_call_expert}}', ['lce_lead_id'], '{{%leads}}', ['id'], 'CASCADE', 'CASCADE');
        $this->addForeignKey('FK-lead_call_expert_lce_agent_user_id', '{{%lead_call_expert}}', ['lce_agent_user_id'], '{{%employees}}', ['id'], 'SET NULL', 'CASCADE');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%lead_call_expert}}');
    }


}
