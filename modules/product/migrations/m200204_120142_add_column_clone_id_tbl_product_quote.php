<?php

namespace modules\product\migrations;

use yii\db\Migration;
use Yii;

/**
 * Class m200204_120142_add_column_clone_id_tbl_product_quote
 */
class m200204_120142_add_column_clone_id_tbl_product_quote extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%product_quote}}', 'pq_clone_id', $this->integer());

        $this->addForeignKey(
            'FK-product_quote_pq_clone_id',
            '{{%product_quote}}',
            'pq_clone_id',
            '{{%product_quote}}',
            'pq_id',
            'SET NULL',
            'CASCADE'
        );

        Yii::$app->db->getSchema()->refreshTableSchema('{{%product_quote}}');

        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('FK-product_quote_pq_clone_id', '{{%product_quote}}');
        $this->dropColumn('{{%product_quote}}', 'pq_clone_id');

        Yii::$app->db->getSchema()->refreshTableSchema('{{%product_quote}}');

        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }
    }
}
