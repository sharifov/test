<?php

use yii\db\Migration;

/**
 * Class m190925_122208_drop_filed_phone_tbl_sources
 */
class m190925_122208_drop_filed_phone_tbl_sources extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->dropColumn('{{%sources}}', 'phone_number');

        Yii::$app->db->getSchema()->refreshTableSchema('{{%sources}}');

        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->addColumn('{{%sources}}', 'phone_number', $this->string(20));

        Yii::$app->db->getSchema()->refreshTableSchema('{{%sources}}');

        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }
    }
}
