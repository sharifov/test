<?php

use yii\db\Migration;

/**
 * Class m200329_131656_drop_column_c_is_deleted_tbl_call
 */
class m200329_131656_drop_column_c_is_deleted_tbl_call extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->dropColumn('{{%call}}', 'c_is_deleted');

        Yii::$app->db->getSchema()->refreshTableSchema('{{%call}}');
        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->addColumn('{{%call}}', 'c_is_deleted', $this->boolean()->defaultValue(false));

        Yii::$app->db->getSchema()->refreshTableSchema('{{%call}}');
        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }
    }


}
