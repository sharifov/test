<?php

namespace modules\flight\migrations;

use Yii;
use yii\db\Migration;

/**
 * Class m200222_122621_add_columns_tbl_flight
 */
class m200222_122621_add_columns_tbl_flight extends Migration
{
    public function safeUp()
    {
        $this->addColumn('{{%flight}}', 'fl_stops', $this->tinyInteger(1)->null()->defaultValue(null));
        $this->addColumn('{{%flight}}', 'fl_delayed_charge', $this->boolean()->null()->defaultValue(false));

        Yii::$app->db->getSchema()->refreshTableSchema('{{%flight}}');

        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%flight}}', 'fl_stops');
        $this->dropColumn('{{%flight}}', 'fl_delayed_charge');

        Yii::$app->db->getSchema()->refreshTableSchema('{{%flight}}');

        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }
    }
}
