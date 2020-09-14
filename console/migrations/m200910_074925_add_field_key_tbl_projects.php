<?php

use yii\db\Migration;

/**
 * Class m200910_074925_add_field_key_tbl_projects
 */
class m200910_074925_add_field_key_tbl_projects extends Migration
{
    public function safeUp()
    {
        $this->addColumn('{{%projects}}', 'project_key', $this->string(50)->unique());
        $this->createIndex('IND-projects-project_key','{{%projects}}', 'project_key');

        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }

        Yii::$app->db->getSchema()->refreshTableSchema('{{%projects}}');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropIndex('IND-projects-project_key', '{{%projects}}');
        $this->dropColumn('{{%projects}}', 'project_key');

        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }

        Yii::$app->db->getSchema()->refreshTableSchema('{{%projects}}');
    }
}
