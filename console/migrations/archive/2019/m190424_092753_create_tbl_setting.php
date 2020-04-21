<?php

use yii\db\Migration;

/**
 * Class m190424_092753_create_tbl_params
 */
class m190424_092753_create_tbl_setting extends Migration
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


        $this->createTable('{{%setting}}', [
            's_id' => $this->primaryKey(),
            's_key' => $this->string(255)->unique()->notNull(),
            's_name' => $this->string(255),
            's_type' => $this->string(10)->notNull(),
            's_value' => $this->string(255)->notNull(),
            's_updated_dt' => $this->dateTime(),
            's_updated_user_id' => $this->integer(),
        ], $tableOptions);

        $this->addForeignKey('FK-setting_s_updated_user_id', '{{%setting}}', ['s_updated_user_id'], '{{%employees}}', ['id'], 'SET NULL', 'CASCADE');


        $this->insert('{{%setting}}', [
            's_key' => 'enable_lead_inbox',
            's_name' => 'Enable Lead Inbox',
            's_type' => \common\models\Setting::TYPE_BOOL,
            's_value' => '1',
            's_updated_dt' => date('Y-m-d H:i:s'),
        ]);

        /*$this->insert('{{%setting}}', [
            's_key' => 'limit',
            's_name' => 'Limit',
            's_type' => \common\models\Setting::TYPE_INT,
            's_value' => '123',
            's_updated_dt' => date('Y-m-d H:i:s'),
        ]);

        $this->insert('{{%setting}}', [
            's_key' => 'price',
            's_name' => 'Price',
            's_type' => \common\models\Setting::TYPE_DOUBLE,
            's_value' => '1034.56',
            's_updated_dt' => date('Y-m-d H:i:s'),
        ]);*/

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%setting}}');
    }


}
