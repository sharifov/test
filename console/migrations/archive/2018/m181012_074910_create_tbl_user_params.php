<?php

use yii\db\Migration;

/**
 * Class m181012_074910_create_tbl_user_params
 */
class m181012_074910_create_tbl_user_params extends Migration
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

        $this->createTable('user_params', [
            'up_user_id' => $this->integer()->notNull(),
            'up_commission_percent' => $this->integer(3)->defaultValue(10),
            'up_base_amount' => $this->decimal(10, 2)->defaultValue(200),
            'up_updated_dt' => $this->dateTime(),
            'up_updated_user_id' => $this->integer(),
        ], $tableOptions);


        $this->addPrimaryKey('user_params_pk', '{{%user_params}}', ['up_user_id']);
        $this->addForeignKey('user_params_up_user_id_fkey', '{{%user_params}}', ['up_user_id'], '{{%employees}}', ['id'], 'CASCADE', 'CASCADE');
        $this->addForeignKey('user_params_up_updated_user_id_fkey', '{{%user_params}}', ['up_updated_user_id'], '{{%employees}}', ['id'], 'SET NULL', 'CASCADE');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%user_params}}');
    }

}
