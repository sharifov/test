<?php

use yii\db\Migration;

/**
 * Class m210318_073144_alter_tbl_client_chat_request_column_ccr_id
 */
class m210318_073144_alter_tbl_client_chat_request_column_ccr_id extends Migration
{
    public function init()
    {
        $this->db = 'db_postgres';
        parent::init();
    }
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->db->createCommand('alter table client_chat_request alter column "ccr_id" type bigint')->execute();
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->db->createCommand('alter table client_chat_request alter column "ccr_id" type integer')->execute();
    }
}
