<?php

use yii\db\Migration;

/**
 * Class m210407_085014_alter_tabl_quote_segment_qs_air_equip_type
 */
class m210407_085014_alter_tabl_quote_segment_qs_air_equip_type extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn('{{%quote_segment}}', 'qs_air_equip_type', $this->string(30));
        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }

        Yii::$app->db->getSchema()->refreshTableSchema('{{%quote_segment}}');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->alterColumn('{{%quote_segment}}', 'qs_air_equip_type', $this->string(30));
        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }

        Yii::$app->db->getSchema()->refreshTableSchema('{{%quote_segment}}');
    }
}
