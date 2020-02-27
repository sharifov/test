<?php

namespace modules\product\migrations;

use Yii;
use yii\db\Migration;

/**
 * Class m200222_084214_add_columns_tbl_product
 */
class m200222_084214_add_columns_tbl_product extends Migration
{

    public function safeUp()
    {
        $this->addColumn('{{%product}}', 'pr_market_price', $this->decimal(8, 2)->null()->defaultValue(null));
        $this->addColumn('{{%product}}', 'pr_client_budget', $this->decimal(8, 2)->null()->defaultValue(null));

        Yii::$app->db->getSchema()->refreshTableSchema('{{%product}}');

        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%product}}', 'pr_market_price');
        $this->dropColumn('{{%product}}', 'pr_client_budget');

        Yii::$app->db->getSchema()->refreshTableSchema('{{%product}}');

        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }
    }
}
