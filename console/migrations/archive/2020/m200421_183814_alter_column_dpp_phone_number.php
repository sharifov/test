<?php

use yii\db\Migration;

/**
 * Class m200421_183814_alter_column_dpp_phone_number
 */
class m200421_183814_alter_column_dpp_phone_number extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn('{{%department_phone_project}}', 'dpp_phone_number', $this->string(18));
        $this->dropIndex('IND-department_phone_project_dpp_phone_number', '{{%department_phone_project}}');

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
        $this->alterColumn('{{%department_phone_project}}', 'dpp_phone_number', $this->string(18)->notNull()->unique());
        $this->createIndex('IND-department_phone_project_dpp_phone_number', '{{%department_phone_project}}', ['dpp_phone_number']);

        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }

        Yii::$app->db->getSchema()->refreshTableSchema('{{%department_phone_project}}');
    }
}
