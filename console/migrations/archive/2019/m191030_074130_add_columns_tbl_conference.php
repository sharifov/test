<?php

use yii\db\Migration;

/**
 * Class m191030_074130_add_columns_tbl_conference
 */
class m191030_074130_add_columns_tbl_conference extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%conference}}', 'cf_recording_url', $this->string(200));
        $this->addColumn('{{%conference}}', 'cf_recording_duration', $this->integer());
        $this->addColumn('{{%conference}}', 'cf_recording_sid', $this->string(34));

        $this->createIndex('IND-conference_cf_recording_sid', '{{%conference}}', 'cf_recording_sid', true);

        Yii::$app->db->getSchema()->refreshTableSchema('{{%conference}}');

        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropIndex('IND-conference_cf_recording_sid', '{{%conference}}');

        $this->dropColumn('{{%conference}}', 'cf_recording_url');
        $this->dropColumn('{{%conference}}', 'cf_recording_duration');
        $this->dropColumn('{{%conference}}', 'cf_recording_sid');

        Yii::$app->db->getSchema()->refreshTableSchema('{{%conference}}');

        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }
    }


}
