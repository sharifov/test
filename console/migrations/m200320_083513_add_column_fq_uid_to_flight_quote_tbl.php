<?php

use yii\db\Migration;

/**
 * Class m200320_083513_add_column_fq_uid_to_flight_quote_tbl
 */
class m200320_083513_add_column_fq_uid_to_flight_quote_tbl extends Migration
{
    public function safeUp()
    {
        $this->addColumn('{{%quotes}}', 'fq_uid', $this->string(50));

        Yii::$app->db->getSchema()->refreshTableSchema('{{%quotes}}');
        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%quotes}}', 'fq_uid');

        Yii::$app->db->getSchema()->refreshTableSchema('{{%quotes}}');
        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }
    }
}
