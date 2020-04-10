<?php

use yii\db\Migration;

/**
 * Class m200329_121024_update_IND_call_c_recording_sid
 */
class m200329_121024_update_IND_call_c_recording_sid extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->dropIndex('IND-call_c_recording_sid', '{{%call}}');
        $this->createIndex('IND-call_c_recording_sid', '{{%call}}', 'c_recording_sid');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropIndex('IND-call_c_recording_sid', '{{%call}}');
        $this->createIndex('IND-call_c_recording_sid', '{{%call}}', 'c_recording_sid', true);
    }
}
