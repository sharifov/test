<?php

use yii\db\Migration;

/**
 * Class m220711_065332_add_new_object_segment_type
 */
class m220711_065332_add_new_object_segment_type extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->insert('{{%object_segment_types}}', [
            'ost_key' => 'client',
            'ost_created_dt' => date('Y-m-d H:i:s')
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->delete('{{%object_segment_types}}', ['ost_key' => 'client']);
    }
}
