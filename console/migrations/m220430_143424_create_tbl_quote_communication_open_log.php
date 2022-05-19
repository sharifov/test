<?php

use yii\db\Migration;

/**
 * Class m220430_143424_create_tbl_quote_communication_open_log
 */
class m220430_143424_create_tbl_quote_communication_open_log extends Migration
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

        $this->createTable('{{%quote_communication_open_log}}', [
            '[[qcol_id]]' => $this->primaryKey(),
            '[[qcol_quote_communication_id]]' => $this->integer()->notNull(),
            '[[qcol_created_dt]]' => $this->timestamp()
        ], $tableOptions);

        $this->addForeignKey('FK-quote_communication_open_log-qcol_quote_communication_id', '{{%quote_communication_open_log}}', '[[qcol_quote_communication_id]]', '{{%quote_communication}}', '[[qc_id]]');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('FK-quote_communication_open_log-qcol_quote_communication_id', '{{%quote_communication_open_log}}');

        $this->dropTable('{{%quote_communication_open_log}}');
    }
}
