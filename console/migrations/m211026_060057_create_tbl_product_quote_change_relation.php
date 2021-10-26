<?php

use yii\db\Migration;

/**
 * Class m211026_060057_create_tbl_product_quote_change_relation
 */
class m211026_060057_create_tbl_product_quote_change_relation extends Migration
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
        $this->createTable('{{%product_quote_change_relation}}', [
            'pqcr_pqc_id' => $this->integer()->notNull(),
            'pqcr_pq_id' => $this->integer()->notNull(),
        ], $tableOptions);

        $this->addPrimaryKey('PK-product_quote_change_relation', '{{%product_quote_change_relation}}', ['pqcr_pqc_id', 'pqcr_pq_id']);
        $this->addForeignKey(
            'FK-product_quote_change_relation-pqcr_pqc_id',
            '{{%product_quote_change_relation}}',
            ['pqcr_pqc_id'],
            '{{%product_quote_change}}',
            ['pqc_id'],
            'CASCADE',
            'CASCADE'
        );

        $this->addForeignKey(
            'FK-product_quote_change_relation-pqcr_pq_id',
            '{{%product_quote_change_relation}}',
            ['pqcr_pq_id'],
            '{{%product_quote}}',
            ['pq_id'],
            'CASCADE',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('FK-product_quote_change_relation-pqcr_pqc_id', '{{%product_quote_change_relation}}');
        $this->dropForeignKey('FK-product_quote_change_relation-pqcr_pq_id', '{{%product_quote_change_relation}}');
        $this->dropTable('{{%product_quote_change_relation}}');
    }
}
