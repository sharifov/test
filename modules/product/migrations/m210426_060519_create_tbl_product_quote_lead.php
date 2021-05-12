<?php

namespace modules\product\migrations;

use yii\db\Migration;

/**
 * Class m210426_060519_create_tbl_product_quote_lead
 */
class m210426_060519_create_tbl_product_quote_lead extends Migration
{
    private string $tableName = '{{%product_quote_lead}}';

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
            'pql_product_quote_id' => $this->integer(),
            'pql_lead_id' => $this->integer()
        ], $tableOptions);

        $this->addPrimaryKey('PK-product_quote_lead', $this->tableName, ['pql_product_quote_id', 'pql_lead_id']);
        $this->addForeignKey('FK-product_quote_lead-pql_product_quote_id', $this->tableName, 'pql_product_quote_id', '{{%product_quote}}', 'pq_id', 'CASCADE', 'CASCADE');
        $this->addForeignKey('FK-product_quote_lead-pql_lead_id', $this->tableName, 'pql_lead_id', '{{%leads}}', 'id', 'CASCADE', 'CASCADE');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('FK-product_quote_lead-pql_product_quote_id', $this->tableName);
        $this->dropForeignKey('FK-product_quote_lead-pql_lead_id', $this->tableName);
        $this->dropTable($this->tableName);
    }
}
