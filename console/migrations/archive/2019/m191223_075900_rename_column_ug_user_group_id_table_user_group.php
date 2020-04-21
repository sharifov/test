<?php

use yii\db\Migration;

/**
 * Class m191223_075900_rename_column_ug_user_group_id_table_user_group
 */
class m191223_075900_rename_column_ug_user_group_id_table_user_group extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->dropForeignKey('FK-user_group_ug_user_group_id', '{{%user_group}}');
        $this->renameColumn('{{%user_group}}', 'ug_user_group_id', 'ug_user_group_set_id');
        $this->addForeignKey(
            'FK-user_group_ug_user_group_set_id',
            '{{%user_group}}',
            'ug_user_group_set_id',
            '{{%user_group_set}}',
            'ugs_id',
            'SET NULL',
            'CASCADE'
        );

        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }

        Yii::$app->db->getSchema()->refreshTableSchema('{{%user_group}}');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('FK-user_group_ug_user_group_set_id', '{{%user_group}}');
        $this->renameColumn('{{%user_group}}', 'ug_user_group_set_id', 'ug_user_group_id');
        $this->addForeignKey(
            'FK-user_group_ug_user_group_id',
            '{{%user_group}}',
            'ug_user_group_id',
            '{{%user_group_set}}',
            'ugs_id',
            'SET NULL',
            'CASCADE'
        );

        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }

        Yii::$app->db->getSchema()->refreshTableSchema('{{%user_group}}');
    }
}
