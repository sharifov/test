<?php

namespace modules\product\migrations;

use Yii;
use yii\db\Migration;

/**
 * Class m200127_142349_add_column_pqsl_action_id_tbl_product_quote_status_log
 */
class m200127_142349_add_column_pqsl_action_id_tbl_product_quote_status_log extends Migration
{    /**
 * {@inheritdoc}
 */
    public function safeUp()
    {
        $this->addColumn('{{%product_quote_status_log}}', 'pqsl_action_id', $this->tinyInteger());

        Yii::$app->db->getSchema()->refreshTableSchema('{{%product_quote_status_log}}');

        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%product_quote_status_log}}', 'pqsl_action_id');

        Yii::$app->db->getSchema()->refreshTableSchema('{{%product_quote_status_log}}');

        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }
    }
}
