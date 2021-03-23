<?php

namespace modules\product\migrations;

use yii\db\Migration;

/**
 * Class m210309_151500_create_tbl_product_holder
 */
class m210309_151500_create_tbl_product_holder extends Migration
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

        $this->createTable('{{%product_holder}}', [
            'ph_id' => $this->primaryKey(),
            'ph_product_id' => $this->integer(),
            'ph_first_name' => $this->string(50),
            'ph_last_name' => $this->string(50),
            'ph_email' => $this->string(100),
            'ph_phone_number' => $this->string(20),
            'ph_created_dt' => $this->dateTime(),
        ], $tableOptions);

        $this->addForeignKey('FK-product_holder-ph_product_id', '{{%product_holder}}', 'ph_product_id', '{{%product}}', 'pr_id', 'CASCADE', 'CASCADE');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('FK-product_holder-ph_product_id', '{{%product_holder}}');
        $this->dropTable('{{%product_holder}}');
    }
}
