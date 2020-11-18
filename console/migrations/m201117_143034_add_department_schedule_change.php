<?php

use yii\db\Migration;

/**
 * Class m201117_143034_add_department_schedule_change
 */
class m201117_143034_add_department_schedule_change extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->insert('{{%department}}', [
            'dep_id'             => 4,
            'dep_key'            => 'schedule_change',
            'dep_name'           => 'Schedule Change',
            'dep_updated_user_id' => null,
            'dep_updated_dt'     => date('Y-m-d H:i:s'),
            'dep_params'        => '{"default_phone_type":"Only personal"}',
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->delete('{{%department}}', [
            'dep_id' => 4
        ]);
    }
}
