<?php

use yii\db\Migration;

/**
 * Class m200108_103509_add_column_show_on_site_tbl_department_phone_project
 */
class m200108_103509_add_column_show_on_site_tbl_department_phone_project extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%department_phone_project}}', 'dpp_show_on_site', $this->boolean()->defaultValue(false));
        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }
        Yii::$app->db->getSchema()->refreshTableSchema('{{%department_phone_project}}');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%department_phone_project}}', 'dpp_show_on_site');
        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }
        Yii::$app->db->getSchema()->refreshTableSchema('{{%department_phone_project}}');
    }
}
