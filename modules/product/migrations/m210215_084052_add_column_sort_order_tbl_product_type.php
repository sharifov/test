<?php

namespace modules\product\migrations;

use Yii;
use yii\db\Migration;

/**
 * Class m210215_084052_add_column_sort_order_tbl_product_type
 */
class m210215_084052_add_column_sort_order_tbl_product_type extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%product_type}}', 'pt_sort_order', $this->smallInteger()->defaultValue(1));
        $this->addColumn('{{%product_type}}', 'pt_icon_class', $this->string(50));
        Yii::$app->db->getSchema()->refreshTableSchema('{{%product_type}}');
        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%product_type}}', 'pt_sort_order');
        $this->dropColumn('{{%product_type}}', 'pt_icon_class');
        Yii::$app->db->getSchema()->refreshTableSchema('{{%product_type}}');
        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }
    }
}
