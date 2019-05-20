<?php

use yii\db\Migration;

/**
 * Class m190520_062852_alter_column_call_c_recording_duration
 */
class m190520_062852_alter_column_call_c_recording_duration extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->update('{{%call}}', ['c_recording_duration' => 0], new \yii\db\Expression('c_recording_duration = \'\' '));
        $this->alterColumn('{{%call}}', 'c_recording_duration', $this->integer()->null());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->alterColumn('{{%call}}', 'c_recording_duration', $this->string(20));
    }
}
