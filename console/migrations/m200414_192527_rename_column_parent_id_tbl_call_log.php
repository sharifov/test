<?php

use yii\db\Migration;

/**
 * Class m200414_192527_rename_column_parent_id_tbl_call_log
 */
class m200414_192527_rename_column_parent_id_tbl_call_log extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->renameColumn('{{%call_log}}', 'cl_parent_id', 'cl_group_id');
        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }

        Yii::$app->db->getSchema()->refreshTableSchema('{{%call_log}}');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->renameColumn('{{%call_log}}', 'cl_group_id', 'cl_parent_id');
        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }

        Yii::$app->db->getSchema()->refreshTableSchema('{{%call_log}}');
    }
}
