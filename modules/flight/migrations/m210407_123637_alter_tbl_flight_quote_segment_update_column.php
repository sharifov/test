<?php

namespace modules\flight\migrations;

use Yii;
use yii\db\Migration;

/**
 * Class m210315_123637_alter_tbl_flight_quote_option_add_columns
 */
class m210407_123637_alter_tbl_flight_quote_segment_update_column extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn('{{%flight_quote_segment}}', 'fqs_air_equip_type', $this->string(30));
        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }

        Yii::$app->db->getSchema()->refreshTableSchema('{{%flight_quote_segment}}');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->alterColumn('{{%flight_quote_segment}}', 'fqs_air_equip_type', $this->string(30));
        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }

        Yii::$app->db->getSchema()->refreshTableSchema('{{%flight_quote_segment}}');
    }
}
