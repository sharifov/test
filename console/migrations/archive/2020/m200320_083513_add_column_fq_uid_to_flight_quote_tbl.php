<?php

use yii\db\Migration;

/**
 * Class m200320_083513_add_column_fq_uid_to_flight_quote_tbl
 */
class m200320_083513_add_column_fq_uid_to_flight_quote_tbl extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%flight_quote}}', 'fq_uid', $this->string(50));
        $this->createIndex('IND-flight_quote_fq_uid', '{{%flight_quote}}', 'fq_uid');

        Yii::$app->db->getSchema()->refreshTableSchema('{{%flight_quote}}');
        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropIndex('IND-flight_quote_fq_uid', '{{%flight_quote}}');
        $this->dropColumn('{{%flight_quote}}', 'fq_uid');

        Yii::$app->db->getSchema()->refreshTableSchema('{{%flight_quote}}');
        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }
    }
}
