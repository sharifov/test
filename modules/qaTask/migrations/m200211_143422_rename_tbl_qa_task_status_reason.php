<?php

namespace modules\qaTask\migrations;

use Yii;
use yii\db\Migration;

/**
 * Class m200211_143422_rename_tbl_qa_task_status_reason
 */
class m200211_143422_rename_tbl_qa_task_status_reason extends Migration
{
    public $fields = [
        'tsr_id' => 'tar_id',
        'tsr_object_type_id' => 'tar_object_type_id',
        'tsr_status_id' => 'tar_action_id',
        'tsr_key' => 'tar_key',
        'tsr_name' => 'tar_name',
        'tsr_description' => 'tar_description',
        'tsr_comment_required' => 'tar_comment_required',
        'tsr_enabled' => 'tar_enabled',
        'tsr_created_user_id' => 'tar_created_user_id',
        'tsr_updated_user_id' => 'tar_updated_user_id',
        'tsr_created_dt' => 'tar_created_dt',
        'tsr_updated_dt' => 'tar_updated_dt',
    ];
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->dropIndex('idx-unique-qa_task_status_reason-object-status-name', '{{%qa_task_status_reason}}');
        $this->dropForeignKey('FK-qa_task_status_reason-tsr_updated_user_id', '{{%qa_task_status_reason}}');
        $this->dropForeignKey('FK-qa_task_status_reason-tsr_created_user_id', '{{%qa_task_status_reason}}');

        $this->renameTable('{{%qa_task_status_reason}}', '{{%qa_task_action_reason}}');

        foreach ($this->fields as $from => $to) {
            $this->renameColumn('{{%qa_task_action_reason}}', $from, $to);
        }

        $this->addForeignKey('FK-qa_task_action_reason-tar_created_user_id', '{{%qa_task_action_reason}}', ['tar_created_user_id'], '{{%employees}}', ['id'], 'SET NULL', 'CASCADE');
        $this->addForeignKey('FK-qa_task_action_reason-tar_updated_user_id', '{{%qa_task_action_reason}}', ['tar_updated_user_id'], '{{%employees}}', ['id'], 'SET NULL', 'CASCADE');
        $this->createIndex('idx-unique-qa_task_action_reason-object-action-name', '{{%qa_task_action_reason}}', [
            'tar_object_type_id', 'tar_action_id', 'tar_name'
        ], true);

        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropIndex('idx-unique-qa_task_action_reason-object-action-name', '{{%qa_task_action_reason}}');
        $this->dropForeignKey('FK-qa_task_action_reason-tar_updated_user_id', '{{%qa_task_action_reason}}');
        $this->dropForeignKey('FK-qa_task_action_reason-tar_created_user_id', '{{%qa_task_action_reason}}');

        $this->renameTable('{{%qa_task_action_reason}}', '{{%qa_task_status_reason}}');

        foreach (array_flip($this->fields) as $from => $to) {
            $this->renameColumn('{{%qa_task_status_reason}}', $from, $to);
        }

        $this->addForeignKey('FK-qa_task_status_reason-tsr_created_user_id', '{{%qa_task_status_reason}}', ['tsr_created_user_id'], '{{%employees}}', ['id'], 'SET NULL', 'CASCADE');
        $this->addForeignKey('FK-qa_task_status_reason-tsr_updated_user_id', '{{%qa_task_status_reason}}', ['tsr_updated_user_id'], '{{%employees}}', ['id'], 'SET NULL', 'CASCADE');
        $this->createIndex('idx-unique-qa_task_status_reason-object-status-name', '{{%qa_task_status_reason}}', [
            'tsr_object_type_id', 'tsr_status_id', 'tsr_name'
        ], true);

        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }
    }
}
