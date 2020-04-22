<?php

use yii\db\Migration;

/**
 * Class m190826_055116_add_column_tbl_user_project_param
 */
class m190826_055116_add_column_tbl_user_project_param extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%user_project_params}}', 'upp_dep_id', $this->integer());
        $this->addForeignKey('FK-user_project_params_upp_dep_id', '{{%user_project_params}}', ['upp_dep_id'], '{{%department}}', ['dep_id'], 'SET NULL', 'CASCADE');

        Yii::$app->db->getSchema()->refreshTableSchema('{{%user_project_params}}');

        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('FK-user_project_params_upp_dep_id', '{{%user_project_params}}');

        $this->dropColumn('{{%user_project_params}}', 'upp_dep_id');
        Yii::$app->db->getSchema()->refreshTableSchema('{{%user_project_params}}');

        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }
    }
}
