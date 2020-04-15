<?php

use yii\db\Migration;

/**
 * Class m200410_063941_add_field_tbl_user_connection
 */
class m200410_063941_add_field_tbl_user_connection extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%user_connection}}', 'uc_connection', $this->text());


        Yii::$app->db->getSchema()->refreshTableSchema('{{%user_connection}}');
        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {

        $this->dropColumn('{{%user_connection}}', 'uc_connection');

        Yii::$app->db->getSchema()->refreshTableSchema('{{%user_connection}}');
        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }
    }
}
