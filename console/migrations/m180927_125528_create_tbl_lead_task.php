<?php

use yii\db\Migration;

/**
 * Class m180927_125528_create_tbl_lead_task
 */
class m180927_125528_create_tbl_lead_task extends Migration
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

        $this->createTable('task', [
            't_id' => $this->primaryKey(),
            't_key' => $this->string(100)->unique()->notNull(),
            't_name' => $this->string(100)->notNull(),
            't_description' => $this->string(500),
            't_hidden' => $this->boolean()->defaultValue(false),
        ], $tableOptions);

        $this->createTable('lead_task', [
            'lt_lead_id' => $this->integer()->notNull(),
            'lt_task_id' => $this->integer()->notNull(),
            'lt_user_id' => $this->integer()->notNull(),
            'lt_date' => $this->date()->notNull(),
            'lt_notes' => $this->string(500),
            'lt_completed_dt' => $this->dateTime(),
            'lt_updated_dt' => $this->dateTime(),
        ], $tableOptions);

        $this->addPrimaryKey('lead_task_pk', '{{%lead_task}}', ['lt_user_id', 'lt_lead_id', 'lt_task_id', 'lt_date']);
        $this->addForeignKey('lead_task_lt_user_id_fkey', '{{%lead_task}}', ['lt_user_id'], '{{%employees}}', ['id'], 'CASCADE', 'CASCADE');
        $this->addForeignKey('lead_task_lt_lead_id_fkey', '{{%lead_task}}', ['lt_lead_id'], '{{%leads}}', ['id'], 'CASCADE', 'CASCADE');
        $this->addForeignKey('lead_task_lt_task_id_fkey', '{{%lead_task}}', ['lt_task_id'], '{{%task}}', ['t_id'], 'CASCADE', 'CASCADE');


        $tasks = [];
        $tasks[] = ['t_id' => 1, 't_key' => 'call1', 't_name' => 'Phone Call 1', 't_description' => 'First phone call', 't_hidden' => false];
        $tasks[] = ['t_id' => 2, 't_key' => 'call2', 't_name' => 'Phone Call 2', 't_description' => 'Second phone call', 't_hidden' => false];
        $tasks[] = ['t_id' => 3, 't_key' => 'voice-mail', 't_name' => 'Voice Mail', 't_description' => 'Voice Mail', 't_hidden' => false];
        $tasks[] = ['t_id' => 4, 't_key' => 'email', 't_name' => 'Email', 't_description' => 'Email', 't_hidden' => false];

        foreach ($tasks as $k => $task) {
            $this->insert('{{%task}}', [
                't_id'              => $task['t_id'],
                't_key'             => $task['t_key'],
                't_name'            => $task['t_name'],
                't_description'     => $task['t_description'],
                't_hidden'          => $task['t_hidden'],
            ]);
        }



    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%lead_task}}');
        $this->dropTable('{{%task}}');
    }

}
