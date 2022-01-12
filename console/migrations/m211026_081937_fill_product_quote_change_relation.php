<?php

use modules\product\src\entities\productQuoteChange\ProductQuoteChange;
use modules\product\src\entities\productQuoteRelation\ProductQuoteRelation;
use src\helpers\app\AppHelper;
use yii\db\Migration;

/**
 * Class m211026_081937_fill_product_quote_change_relation
 */
class m211026_081937_fill_product_quote_change_relation extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $items = ProductQuoteRelation::find()
            ->select('pqr_related_pq_id')
            ->addSelect('pqc_id')
            ->innerJoin(ProductQuoteChange::tableName(), 'pqr_parent_pq_id = pqc_pq_id')
            ->groupBy(['pqr_related_pq_id', 'pqc_id'])
            ->orderBy(['pqc_id' => SORT_DESC])
            ->asArray()
            ->all();

        $inserted = [];
        foreach ($items as $key => $value) {
            if (array_key_exists($value['pqr_related_pq_id'], $inserted)) {
                continue;
            }
            $inserted[$value['pqr_related_pq_id']] = $value['pqr_related_pq_id'];
            try {
                $this->insert('{{%product_quote_change_relation}}', [
                    'pqcr_pqc_id' => $value['pqc_id'],
                    'pqcr_pq_id' => $value['pqr_related_pq_id'],
                ]);
            } catch (\Throwable $throwable) {
                Yii::error(AppHelper::throwableLog($throwable), 'fill_product_quote_change_relation:Throwable');
            }
        }
        echo PHP_EOL . 'Inserted count(' . count($inserted) . ')' . PHP_EOL;
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->truncateTable('product_quote_change_relation');
    }
}
