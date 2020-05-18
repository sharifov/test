<?php

use yii\db\Migration;

/**
 * Class m190130_105334_create_tbl_user_call_status
 */
class m190130_105334_create_tbl_user_call_status extends Migration
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



        $this->createTable('{{%user_call_status}}',	[
            'us_id'                 => $this->primaryKey(),
            'us_type_id'            => $this->tinyInteger(1),
            'us_user_id'            => $this->integer(),
            'us_created_dt'         => $this->dateTime(),

        ], $tableOptions);

        $this->createIndex('IND-user_call_status_us_type_id', '{{%user_call_status}}', ['us_type_id']);
        $this->addForeignKey('FK-user_call_status_us_user_id', '{{%user_call_status}}', ['us_user_id'], '{{%employees}}', ['id'], 'SET NULL', 'CASCADE');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%user_call_status}}');
    }


}
