<?php

use yii\db\Migration;

/**
 * Class m190424_143805_call_session_for_gather
 */
class m190424_143805_call_session_for_gather extends Migration
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

        $this->createTable('{{%call_session}}',	[
            'cs_id'             => $this->primaryKey(),
            'cs_call_id'        => $this->integer()->notNull(),
            'cs_cid'            => $this->string(255),
            'cs_step'           => $this->smallInteger(2)->notNull()->defaultValue(1),
            'cs_project_id'        => $this->integer()->notNull(),
            'cs_lang_id'        => $this->smallInteger()->notNull()->defaultValue(1),
            'cs_data_params'    => $this->text()->notNull(),
            'cs_create_dt'      => $this->dateTime(),
            'cs_updated_dt'     => $this->dateTime(),

        ], $tableOptions);

        $this->createIndex('PK-call_session_cs_call_id', '{{%call_session}}', 'cs_call_id');
        $this->createIndex('PK-call_session_cs_cid', '{{%call_session}}', 'cs_cid');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%call_session}}');
    }
}
