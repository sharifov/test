<?php

use yii\db\Migration;

/**
 * Class m191025_131425_add_column_default_phone_table_department_phone_project
 */
class m191025_131425_add_column_default_phone_table_department_phone_project extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%department_phone_project}}', 'dpp_default', $this->boolean());

        Yii::$app->db->getSchema()->refreshTableSchema('{{%department_phone_project}}');
        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%department_phone_project}}', 'dpp_default');

        Yii::$app->db->getSchema()->refreshTableSchema('{{%department_phone_project}}');

        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }
    }
}
