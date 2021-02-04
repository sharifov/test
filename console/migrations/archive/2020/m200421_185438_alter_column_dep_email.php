<?php

use yii\db\Migration;

/**
 * Class m200421_185438_alter_column_dep_email
 */
class m200421_185438_alter_column_dep_email extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn('{{%department_email_project}}', 'dep_email', $this->string(50));
        $this->dropIndex('IND-department_email_project_dep_email', '{{%department_email_project}}');

        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }

        Yii::$app->db->getSchema()->refreshTableSchema('{{%department_email_project}}');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->alterColumn('{{%department_email_project}}', 'dep_email', $this->string(50)->notNull()->unique());
        $this->createIndex('IND-department_email_project_dep_email', '{{%department_email_project}}', ['dep_email']);

        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }

        Yii::$app->db->getSchema()->refreshTableSchema('{{%department_email_project}}');
    }
}
