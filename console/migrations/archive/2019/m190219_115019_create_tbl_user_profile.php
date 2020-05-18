<?php

use yii\db\Migration;

/**
 * Class m190219_115019_create_tbl_user_profile
 */
class m190219_115019_create_tbl_user_profile extends Migration
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

       $this->createTable('{{%user_profile}}',	[
            'up_user_id'            => $this->integer()->notNull(),
            'up_call_type_id'       => $this->tinyInteger()->defaultValue(0),
            'up_sip'                => $this->string(255),
            'up_telegram'           => $this->string(20),
            'up_telegram_enable'    => $this->boolean()->defaultValue(false),
            'up_updated_dt'         => $this->dateTime(),

        ], $tableOptions);

        $this->addPrimaryKey('PK-user_profile_up_user_id', '{{%user_profile}}', 'up_user_id');
        $this->addForeignKey('FK-user_profile_up_user_id', '{{%user_profile}}', ['up_user_id'], '{{%employees}}', ['id'], 'CASCADE', 'CASCADE');

        $this->dropTable('{{%employee_profile}}');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%user_profile}}');
    }
}
