<?php

namespace modules\product\migrations;

use modules\product\src\entities\productQuoteChange\ProductQuoteChange;
use modules\product\src\entities\productQuoteChangeRelation\ProductQuoteChangeRelation;
use modules\product\src\entities\productQuoteRelation\ProductQuoteRelation;
use yii\db\Migration;
use yii\helpers\Console;

/**
 * Class m211201_075123_convert_old_reprotection_quotes_relation
 */
class m211201_075123_convert_old_reprotection_quotes_relation extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $productQuoteRelations = ProductQuoteRelation::find()->select([
            'pqr_parent_pq_id',
            'pqr_related_pq_id',
            'pqr_created_dt'
        ])
            ->reprotection()
            ->leftJoin('{{%product_quote_change_relation}}', 'pqcr_pq_id = pqr_related_pq_id')
            ->andWhere(['pqcr_pq_id' => null])
            ->asArray()
            ->all();

        $n = 1;
        $total = count($productQuoteRelations);
        $converted = 0;
        Console::startProgress(0, $total, 'Counting objects: ', false);
        $batchInsert = [];

        foreach ($productQuoteRelations as $productQuoteRelation) {
            $productQuoteChange = ProductQuoteChange::find()
                ->scheduleChangeType()
                ->andWhere(['pqc_pq_id' => $productQuoteRelation['pqr_parent_pq_id']])
                ->andWhere(['<=', 'pqc_created_dt', $productQuoteRelation['pqr_created_dt']])
                ->orderBy(['pqc_created_dt' => SORT_DESC])->one();

            if (!$productQuoteChange) {
                $productQuoteChange = ProductQuoteChange::find()
                    ->scheduleChangeType()
                    ->andWhere(['pqc_pq_id' => $productQuoteRelation['pqr_parent_pq_id']])
                    ->orderBy(['pqc_created_dt' => SORT_DESC])->one();
            }

            if ($productQuoteChange) {
                $batchInsert[] = [
                    $productQuoteChange->pqc_id,
                    $productQuoteRelation['pqr_related_pq_id']
                ];
                $converted++;
            }

            Console::updateProgress($n, $total);
            $n++;
        }

        \Yii::$app->db->createCommand()->batchInsert(
            '{{%product_quote_change_relation}}',
            ['pqcr_pqc_id', 'pqcr_pq_id'],
            $batchInsert
        )->execute();


        Console::endProgress("done." . PHP_EOL);
        echo Console::renderColoredString('%g --- Converted quotes: ' . $converted . ' from: ' . $total . '; not converted for any reason: ' . ($total - $converted) . ' %n'), PHP_EOL;
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
    }
}
