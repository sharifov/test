<?php

namespace modules\qaTask\migrations;

use Yii;
use yii\db\Migration;

/**
 * Class m200212_120050_add_column_t_project_id_tbl_qa_task
 */
class m200212_120050_add_column_t_project_id_tbl_qa_task extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%qa_task}}', 't_project_id', $this->integer()->null()->after('t_gid'));
        $this->addForeignKey('FK-qa_task-t_project_id', '{{%qa_task}}', 't_project_id', '{{%projects}}', 'id', 'SET NULL', 'CASCADE');

        Yii::$app->db->getSchema()->refreshTableSchema('{{%qa_task}}');

        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('FK-qa_task-t_project_id', '{{%qa_task}}');
        $this->dropColumn('{{%qa_task}}', 't_project_id');

        Yii::$app->db->getSchema()->refreshTableSchema('{{%qa_task}}');

        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }
    }
}
