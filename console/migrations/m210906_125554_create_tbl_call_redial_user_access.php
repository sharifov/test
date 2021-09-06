<?php

use yii\db\Migration;

/**
 * Class m210906_125554_create_tbl_call_redial_user_access
 */
class m210906_125554_create_tbl_call_redial_user_access extends Migration
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

        $this->createTable('{{%call_redial_user_access}}', [
            'crua_lead_id' => $this->integer(),
            'crua_user_id' => $this->integer()->notNull(),
            'crua_created_dt' => $this->dateTime()->notNull(),
            'crua_updated_dt' => $this->dateTime(),
        ], $tableOptions);

        $this->addPrimaryKey('PK-call_redial_user_access-lead_id', '{{%call_redial_user_access}}', ['crua_lead_id', 'crua_user_id']);
        $this->addForeignKey(
            'FK-call_redial_user_access-lead_id',
            '{{%call_redial_user_access}}',
            'crua_lead_id',
            '{{%lead_qcall}}',
            'lqc_lead_id',
            'CASCADE',
            'CASCADE'
        );
        $this->addForeignKey(
            'FK-call_redial_user_access-user_id',
            '{{%call_redial_user_access}}',
            'crua_user_id',
            '{{%employees}}',
            'id',
            'CASCADE',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%call_redial_user_access}}');
    }
}
