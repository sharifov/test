<?php

namespace modules\product\migrations;

use yii\db\Migration;

/**
 * Class m210826_061136_add_new_table_product_quote_data
 */
class m210826_061136_add_new_table_product_quote_data extends Migration
{
    private string $tableName = '{{%product_quote_data}}';

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable($this->tableName, [
            'pqd_id' => $this->primaryKey(),
            'pqd_product_quote_id' => $this->integer()->notNull(),
            'pqd_key' => $this->tinyInteger()->unsigned()->notNull(),
            'pqd_value' => $this->string(50),
            'pqd_created_dt' => $this->dateTime(),
            'pqd_updated_dt' => $this->dateTime()
        ], $tableOptions);

        $this->addForeignKey('FK-product_quote_data-pqd_product_quote_id', $this->tableName, 'pqd_product_quote_id', '{{%product_quote}}', 'pq_id', 'CASCADE', 'CASCADE');
        $this->createIndex('IND-product_quote_data-pqd_key', $this->tableName, 'pqd_key');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('FK-product_quote_data-pqd_product_quote_id', $this->tableName);
        $this->dropTable($this->tableName);
    }
}
