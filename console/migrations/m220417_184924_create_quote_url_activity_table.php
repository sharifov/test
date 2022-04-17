<?php

use yii\db\Migration;

/**
 * Class m220417_184924_create_quote_url_activity_table
 */
class m220417_184924_create_quote_url_activity_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%quote_url_activity}}', [
            '[[qua_id]]' => $this->primaryKey(),
            '[[qua_uid]]' => $this->string(32)->notNull(),
            '[[qua_quote_id]]' => $this->integer()->notNull(),
            '[[qua_communication_type]]' => $this->smallInteger()->notNull(),
            '[[qua_ext_data]]' => $this->text()->defaultValue(null),
            '[[qua_created_dt]]' => $this->timestamp()
        ], $tableOptions);

        $this->addForeignKey('FK-quote_url_activity-qua_quote_id', '{{%quote_url_activity}}', '[[qua_quote_id]]', '{{%quotes}}', '[[id]]', 'CASCADE', 'CASCADE');
        $this->createIndex('IND-quote_url_activity-qua_uid', '{{%quote_url_activity}}', '[[qua_uid]]', true);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropIndex('IND-quote_url_activity-qua_uid', '{{%quote_url_activity}}');
        $this->dropForeignKey('FK-quote_url_activity-qua_quote_id', '{{%quote_url_activity}}');
        $this->dropTable('{{%quote_url_activity}}');
    }
}
