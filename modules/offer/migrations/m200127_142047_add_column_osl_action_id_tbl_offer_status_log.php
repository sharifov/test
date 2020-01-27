<?php

namespace modules\offer\migrations;

use Yii;
use yii\db\Migration;

/**
 * Class m200127_142047_add_column_osl_action_id_tbl_offer_status_log
 */
class m200127_142047_add_column_osl_action_id_tbl_offer_status_log extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%offer_status_log}}', 'osl_action_id', $this->tinyInteger());

        Yii::$app->db->getSchema()->refreshTableSchema('{{%offer_status_log}}');

        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%offer_status_log}}', 'osl_action_id');

        Yii::$app->db->getSchema()->refreshTableSchema('{{%offer_status_log}}');

        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }
    }
}
