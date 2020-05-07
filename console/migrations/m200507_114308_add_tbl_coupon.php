<?php

use yii\db\Migration;

/**
 * Class m200507_114308_add_tbl_coupon
 */
class m200507_114308_add_tbl_coupon extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%coupon}}', [
            'c_id' => $this->primaryKey(),
            'c_code' => $this->string(50)->notNull(),
            'c_amount' => $this->decimal(8,2),
            'c_currency_code' => $this->string(3),
            'c_percent' => $this->smallInteger(),
            'c_exp_date' => $this->dateTime(),
            'c_start_date' => $this->dateTime(),
            'c_reusable' => $this->boolean(),
            'c_reusable_count' => $this->integer(),
            'c_public' => $this->boolean(),
            'c_status_id' => $this->tinyInteger(),
            'c_used_dt' => $this->dateTime(),
            'c_disabled' => $this->boolean(),
            'c_type_id' => $this->smallInteger(),
            'c_created_dt' => $this->dateTime(),
            'c_updated_dt' => $this->dateTime(),
            'c_created_user_id' => $this->integer(),
            'c_updated_user_id' => $this->integer(),
        ], $tableOptions);

        $this->createIndex('IND-coupon-c_code', '{{%coupon}}', ['c_code'], true);

        //$this->addForeignKey('FK-coupon-c_currency_code', '{{%coupon}}', ['c_currency_code'], '{{%currency}}', ['cur_code'], 'SET NULL', 'CASCADE');
        $this->addForeignKey('FK-coupon-c_created_user_id', '{{%coupon}}', ['c_created_user_id'], '{{%employees}}', ['id'], 'SET NULL', 'CASCADE');
        $this->addForeignKey('FK-coupon-c_updated_user_id', '{{%coupon}}', ['c_updated_user_id'], '{{%employees}}', ['id'], 'SET NULL', 'CASCADE');

        $this->createTable('{{%coupon_case}}', [
            'cc_coupon_id' => $this->integer(),
            'cc_case_id' => $this->integer(),
            'cc_sale_id' => $this->integer(),
            'cc_created_dt' => $this->dateTime(),
            'cc_created_user_id' => $this->integer(),
        ], $tableOptions);

        $this->addPrimaryKey('PK-coupon_case-cc_coupon_id_cc_case_id', '{{%coupon_case}}', ['cc_coupon_id', 'cc_case_id']);

        $this->addForeignKey(
            'FK-coupon_case-cc_coupon_id',
            '{{%coupon_case}}',
            'cc_coupon_id',
            '{{%coupon}}',
            'c_id',
            'CASCADE',
            'CASCADE'
        );

        $this->addForeignKey(
            'FK-coupon_case-cc_case_id',
            '{{%coupon_case}}',
            'cc_case_id',
            '{{%cases}}',
            'cs_id',
            'CASCADE',
            'CASCADE'
        );

        $this->addForeignKey('FK-coupon_case-cc_created_user_id', '{{%coupon_case}}', ['cc_created_user_id'], '{{%employees}}', ['id'], 'SET NULL', 'CASCADE');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%coupon_case}}');
        $this->dropTable('{{%coupon}}');
    }
}
