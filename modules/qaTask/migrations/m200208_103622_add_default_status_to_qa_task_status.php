<?php

namespace modules\qaTask\migrations;

use Yii;
use yii\db\Migration;

/**
 * Class m200208_103622_add_default_status_to_qa_task_status
 */
class m200208_103622_add_default_status_to_qa_task_status extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->insert('{{%qa_task_status}}', [
            'ts_id' => 1,
            'ts_name' => 'Pending',
            'ts_description' => '',
            'ts_enabled' => true,
            'ts_css_class' => 'info',
            'ts_created_user_id' => null,
            'ts_updated_user_id' => null,
            'ts_created_dt' => date('Y-m-d H:i:s'),
            'ts_updated_dt' => date('Y-m-d H:i:s'),
        ]);
        $this->insert('{{%qa_task_status}}', [
            'ts_id' => 2,
            'ts_name' => 'Processing',
            'ts_description' => '',
            'ts_enabled' => true,
            'ts_css_class' => 'success',
            'ts_created_user_id' => null,
            'ts_updated_user_id' => null,
            'ts_created_dt' => date('Y-m-d H:i:s'),
            'ts_updated_dt' => date('Y-m-d H:i:s'),
        ]);
        $this->insert('{{%qa_task_status}}', [
            'ts_id' => 3,
            'ts_name' => 'Escalated',
            'ts_description' => '',
            'ts_enabled' => true,
            'ts_css_class' => 'primary',
            'ts_created_user_id' => null,
            'ts_updated_user_id' => null,
            'ts_created_dt' => date('Y-m-d H:i:s'),
            'ts_updated_dt' => date('Y-m-d H:i:s'),
        ]);
        $this->insert('{{%qa_task_status}}', [
            'ts_id' => 4,
            'ts_name' => 'Closed',
            'ts_description' => '',
            'ts_enabled' => true,
            'ts_css_class' => 'warning',
            'ts_created_user_id' => null,
            'ts_updated_user_id' => null,
            'ts_created_dt' => date('Y-m-d H:i:s'),
            'ts_updated_dt' => date('Y-m-d H:i:s'),
        ]);
        $this->insert('{{%qa_task_status}}', [
            'ts_id' => 5,
            'ts_name' => 'Canceled',
            'ts_description' => '',
            'ts_enabled' => true,
            'ts_css_class' => 'danger',
            'ts_created_user_id' => null,
            'ts_updated_user_id' => null,
            'ts_created_dt' => date('Y-m-d H:i:s'),
            'ts_updated_dt' => date('Y-m-d H:i:s'),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->delete('{{%qa_task_status}}', 'ts_id = 1');
        $this->delete('{{%qa_task_status}}', 'ts_id = 2');
        $this->delete('{{%qa_task_status}}', 'ts_id = 3');
        $this->delete('{{%qa_task_status}}', 'ts_id = 4');
        $this->delete('{{%qa_task_status}}', 'ts_id = 5');
    }
}
