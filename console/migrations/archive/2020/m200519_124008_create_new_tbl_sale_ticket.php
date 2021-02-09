<?php

use yii\db\Migration;

/**
 * Class m200519_124008_create_new_tbl_sale_ticket
 */
class m200519_124008_create_new_tbl_sale_ticket extends Migration
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

        $this->createTable('{{%sale_ticket}}', [
            'st_id' => $this->primaryKey(),
            'st_case_id' => $this->integer()->notNull(),
            'st_case_sale_id' => $this->integer()->notNull(),
            'st_ticket_number' => $this->string(15),
            'st_client_name' => $this->string(50),
            'st_record_locator' => $this->string(8),
            'st_original_fop' => $this->string(5),
            'st_charge_system' => $this->tinyInteger(2),
            'st_penalty_type' => $this->string(30),
            'st_penalty_amount' => $this->decimal(8, 2),
            'st_selling' => $this->decimal(8, 2),
            'st_service_fee' => $this->decimal(8, 2),
            'st_recall_commission' => $this->decimal(8, 2),
            'st_markup' => $this->decimal(8, 2),
            'st_upfront_charge' => $this->decimal(8, 2),
            'st_refundable_amount' => $this->decimal(8, 2),
            'st_created_dt' => $this->dateTime(),
            'st_updated_dt' => $this->dateTime(),
            'st_created_user_id' => $this->integer(),
            'st_updated_user_id' => $this->integer(),
        ], $tableOptions);

        $this->addForeignKey('FK-st_case_id', '{{%sale_ticket}}', ['st_case_id'], '{{%cases}}', ['cs_id'], 'CASCADE', 'CASCADE');
        $this->addForeignKey('FK-st_case_sale_id_st_case_id', '{{%sale_ticket}}', ['st_case_id', 'st_case_sale_id'], '{{%case_sale}}', ['css_cs_id', 'css_sale_id'], 'CASCADE', 'CASCADE');
        $this->addForeignKey('FK-sale_ticket_st_created_user_id', '{{%sale_ticket}}', ['st_created_user_id'], '{{%employees}}', ['id'], 'SET NULL', 'CASCADE');
        $this->addForeignKey('FK-sale_ticket_st_updated_user_id', '{{%sale_ticket}}', ['st_updated_user_id'], '{{%employees}}', ['id'], 'SET NULL', 'CASCADE');


        if (\Yii::$app->cache) {
            \Yii::$app->cache->flush();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%sale_ticket}}');
        if (\Yii::$app->cache) {
            \Yii::$app->cache->flush();
        }
    }
}
