<?php

use yii\db\Migration;

/**
 * Class m181003_125732_add_column_category_tbl_lead_task
 */
class m181003_125732_add_column_category_tbl_lead_task extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%task}}', 't_category_id', $this->tinyInteger());
        $this->addColumn('{{%task}}', 't_sort_order', $this->tinyInteger()->defaultValue(10));


        $tasks = [];
        $tasks[] = ['t_id' => 5, 't_key' => 'book-call1', 't_name' => 'Book Phone Call 1', 't_description' => 'First Book phone call', 't_hidden' => false];
        $tasks[] = ['t_id' => 6, 't_key' => 'book-call2', 't_name' => 'Book Phone Call 2', 't_description' => 'Second Book phone call', 't_hidden' => false];
        $tasks[] = ['t_id' => 7, 't_key' => 'book-voice-mail', 't_name' => 'Book Voice Mail', 't_description' => 'Book Voice Mail', 't_hidden' => false];
        $tasks[] = ['t_id' => 8, 't_key' => 'book-email', 't_name' => 'Book Email', 't_description' => 'Book Email', 't_hidden' => false];

        $sort = 11;

        foreach ($tasks as $k => $task) {
            $sort++;
            $this->insert('{{%task}}', [
                't_id'              => $task['t_id'],
                't_key'             => $task['t_key'],
                't_name'            => $task['t_name'],
                't_description'     => $task['t_description'],
                't_hidden'          => $task['t_hidden'],
                't_category_id'          => 2,
                't_sort_order'          => $sort++,
            ]);
        }

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%task}}', 't_category_id');
        $this->dropColumn('{{%task}}', 't_sort_order');
    }
}
