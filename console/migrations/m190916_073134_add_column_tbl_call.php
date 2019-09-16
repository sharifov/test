<?php

use yii\db\Migration;

/**
 * Class m190916_073134_add_column_tbl_call
 */
class m190916_073134_add_column_tbl_call extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%call}}', 'c_recording_sid', $this->string(34));
        $this->createIndex('IND-call_c_recording_sid', '{{%call}}', 'c_recording_sid', true);


        $this->dropColumn('{{%call}}', 'c_sequence_number');
        $this->addColumn('{{%call}}', 'c_sequence_number', $this->smallInteger());

        Yii::$app->db->getSchema()->refreshTableSchema('{{%call}}');

        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropIndex('IND-call_c_recording_sid', '{{%call}}');
        $this->dropColumn('{{%call}}', 'c_recording_sid');

        Yii::$app->db->getSchema()->refreshTableSchema('{{%call}}');

        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }
    }

}
