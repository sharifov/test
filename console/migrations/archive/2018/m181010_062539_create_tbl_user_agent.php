<?php

use yii\db\Migration;

/**
 * Class m181010_062539_create_tbl_user_agent
 */
class m181010_062539_create_tbl_user_agent extends Migration
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

        $this->createTable('user_group', [
            'ug_id' => $this->primaryKey(),
            'ug_key' => $this->string(100)->unique()->notNull(),
            'ug_name' => $this->string(100)->notNull(),
            'ug_description' => $this->string(255),
            'ug_disable' => $this->boolean()->defaultValue(false),
            'ug_updated_dt' => $this->dateTime(),
        ], $tableOptions);

        $this->createTable('user_group_assign', [
            'ugs_user_id' => $this->integer()->notNull(),
            'ugs_group_id' => $this->integer()->notNull(),
            'ugs_updated_dt' => $this->dateTime(),
        ], $tableOptions);

        $this->addPrimaryKey('user_group_assign_pk', '{{%user_group_assign}}', ['ugs_user_id', 'ugs_group_id']);
        $this->addForeignKey('user_group_assign_ugs_user_id_fkey', '{{%user_group_assign}}', ['ugs_user_id'], '{{%employees}}', ['id'], 'CASCADE', 'CASCADE');
        $this->addForeignKey('user_group_assign_ugs_group_id_fkey', '{{%user_group_assign}}', ['ugs_group_id'], '{{%user_group}}', ['ug_id'], 'CASCADE', 'CASCADE');



        $ug = [];
        $ug[] = ['ug_id' => 1, 'ug_key' => 'ug1', 'ug_name' => 'User Group 1', 'ug_description' => 'User Group 1', 'ug_disable' => false];
        $ug[] = ['ug_id' => 2, 'ug_key' => 'ug2', 'ug_name' => 'User Group 2', 'ug_description' => 'User Group 2', 'ug_disable' => false];
        $ug[] = ['ug_id' => 3, 'ug_key' => 'ug3', 'ug_name' => 'User Group 3', 'ug_description' => 'User Group 3', 'ug_disable' => true];

        foreach ($ug as $k => $ugItem) {
            $this->insert('{{%user_group}}', [
                'ug_id'              => $ugItem['ug_id'],
                'ug_key'             => $ugItem['ug_key'],
                'ug_name'            => $ugItem['ug_name'],
                'ug_description'     => $ugItem['ug_description'],
                'ug_disable'          => $ugItem['ug_disable'],
                'ug_updated_dt'          => date('Y-m-d H:i:s'),
            ]);
        }

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%user_group_assign}}');
        $this->dropTable('{{%user_group}}');
    }


}
