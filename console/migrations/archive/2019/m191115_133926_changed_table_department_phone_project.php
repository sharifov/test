<?php

use yii\db\Migration;

/**
 * Class m191115_133926_changed_table_department_phone_project
 */
class m191115_133926_changed_table_department_phone_project extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->renameColumn('{{%department_phone_project}}', 'dpp_default', 'dpp_redial');
        $this->addColumn('{{%department_phone_project}}', 'dpp_description', $this->string(255));
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
        $this->dropColumn('{{%department_phone_project}}', 'dpp_description');
        $this->renameColumn('{{%department_phone_project}}', 'dpp_redial', 'dpp_default');
        Yii::$app->db->getSchema()->refreshTableSchema('{{%department_phone_project}}');
        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }
    }
}
