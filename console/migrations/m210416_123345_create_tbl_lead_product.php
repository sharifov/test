<?php

use yii\db\Migration;

/**
 * Class m210416_123345_create_tbl_lead_product
 */
class m210416_123345_create_tbl_lead_product extends Migration
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

        $this->createTable('{{%lead_product}}', [
            'lp_lead_id' => $this->integer(),
            'lp_product_id' => $this->integer(),
            'lp_quote_id' => $this->integer()
        ], $tableOptions);

        $this->addPrimaryKey(
            'PK-lead_product-lp_lead_id-lp_product_id',
            '{{%lead_product}}',
            [
                'lp_lead_id',
                'lp_product_id'
            ]
        );

        $this->addForeignKey('FK-lead_product-lp_lead_id', '{{%lead_product}}', 'lp_lead_id', '{{%leads}}', 'id', 'CASCADE', 'CASCADE');
        $this->addForeignKey('FK-lead_product-lp_product_id', '{{%lead_product}}', 'lp_product_id', '{{%product}}', 'pr_id', 'CASCADE', 'CASCADE');
        $this->addForeignKey('FK-lead_product-lp_quote_id', '{{%lead_product}}', 'lp_quote_id', '{{%product_quote}}', 'pq_id', 'CASCADE', 'CASCADE');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('FK-lead_product-lp_lead_id', '{{%lead_product}}');
        $this->dropForeignKey('FK-lead_product-lp_product_id', '{{%lead_product}}');
        $this->dropForeignKey('FK-lead_product-lp_quote_id', '{{%lead_product}}');
        $this->dropTable('{{%lead_product}}');
    }
}
