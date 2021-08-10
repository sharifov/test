<?php

namespace modules\product\migrations;

use yii\db\Migration;

/**
 * Class m210810_123142_create_tbl_product_quote_change
 */
class m210810_123142_create_tbl_product_quote_change extends Migration
{
    private string $tableName = '{{%product_quote_change}}';

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
            'pqc_id' => $this->primaryKey(),
            'pqc_pq_id' => $this->integer()->notNull(),
            'pqc_case_id' => $this->integer(),
            'pqc_decision_user' => $this->integer(),
            'pqc_status_id' => $this->tinyInteger(),
            'pqc_decision_type_id' => $this->integer(),
            'pqc_created_dt' => $this->dateTime(),
            'pqc_updated_dt' => $this->dateTime(),
            'pqc_decision_dt' => $this->dateTime(),

        ], $tableOptions);

        $this->addForeignKey('FK-product_quote_change-pqc_case_id', $this->tableName, 'pqc_case_id', '{{%cases}}', 'cs_id', 'SET NULL', 'CASCADE');
        $this->addForeignKey('FK-product_quote_change-pqc_decision_user', $this->tableName, 'pqc_decision_user', '{{%employees}}', 'id', 'SET NULL', 'CASCADE');
        $this->addForeignKey('FK-product_quote_change-pqc_pq_id', $this->tableName, 'pqc_pq_id', '{{%product_quote}}', 'pq_id', 'CASCADE', 'CASCADE');

        $this->createIndex('IND-product_quote_change-pqc_status_id', $this->tableName, 'pqc_status_id');
        $this->createIndex('IND-product_quote_change-pqc_decision_type_id', $this->tableName, 'pqc_decision_type_id');
        $this->createIndex('IND-product_quote_change-pqc_created_dt', $this->tableName, 'pqc_created_dt');
        $this->createIndex('IND-product_quote_change-pqc_decision_dt', $this->tableName, 'pqc_decision_dt');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('FK-product_quote_change-pqc_case_id', $this->tableName);
        $this->dropForeignKey('FK-product_quote_change-pqc_decision_user', $this->tableName);
        $this->dropForeignKey('FK-product_quote_change-pqc_pq_id', $this->tableName);

        $this->dropTable($this->tableName);
    }
}
