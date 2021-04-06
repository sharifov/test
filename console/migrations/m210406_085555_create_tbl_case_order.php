<?php

use yii\db\Migration;

/**
 * Class m210406_085555_create_tbl_case_order
 */
class m210406_085555_create_tbl_case_order extends Migration
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

        $this->createTable('{{%case_order}}', [
            'co_order_id' => $this->integer(),
            'co_case_id' => $this->integer(),
            'co_create_dt' => $this->dateTime(),
            'co_created_user_id' => $this->integer()
        ], $tableOptions);

        $this->addPrimaryKey(
            'PK-case_order-co_order_id-co_case_id',
            '{{%case_order}}',
            [
                'co_order_id',
                'co_case_id'
            ]
        );

        $this->addForeignKey('PK-case_order-co_order_id', '{{%case_order}}', 'co_order_id', '{{%order}}', 'or_id', 'CASCADE', 'CASCADE');
        $this->addForeignKey('PK-case_order-co_case_id', '{{%case_order}}', 'co_case_id', '{{%cases}}', 'cs_id', 'CASCADE', 'CASCADE');
        $this->addForeignKey('PK-case_order-co_created_user_id', '{{%case_order}}', 'co_created_user_id', '{{%employees}}', 'id', 'SET NULL', 'CASCADE');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('PK-case_order-co_order_id', '{{%case_order}}');
        $this->dropForeignKey('PK-case_order-co_case_id', '{{%case_order}}');
        $this->dropForeignKey('PK-case_order-co_created_user_id', '{{%case_order}}');
        $this->dropTable('{{%case_order}}');
    }
}
