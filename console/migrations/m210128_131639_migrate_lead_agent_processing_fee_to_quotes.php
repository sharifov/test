<?php

use common\models\Lead;
use common\models\Quote;
use sales\helpers\setting\SettingHelper;
use yii\db\Migration;
use yii\db\Query;
use yii\helpers\Console;

/**
 * Class m210128_131639_migrate_lead_agent_processing_fee_to_quotes
 */
class m210128_131639_migrate_lead_agent_processing_fee_to_quotes extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $query = new Query();

        $query->select(['q.id', 'q.lead_id', 'q.status', 'l.agents_processing_fee', 'l.final_profit', 'l.adults', 'l.children'])
            ->from(Quote::tableName() . ' q')
            ->join('inner join', Lead::tableName() . ' l', 'l.id = q.lead_id');

        $quotes = $query->all();

        $n = 1;
        $total = count($quotes);
        Console::startProgress(0, $total, 'Counting objects: ', false);

        foreach ($quotes as $quote) {
            if (empty($quote['agents_processing_fee'])) {
                $quotes['agents_processing_fee'] = ($quote['adults'] + $quote['children']) * SettingHelper::processingFee();
            }
            Yii::$app->db->createCommand()
                ->update(
                    Quote::tableName(),
                    ['agent_processing_fee' => $quote['agents_processing_fee']],
                    'id = :qId',
                    [':qId' => $quote['id']]
                )
                ->execute();

            Console::updateProgress($n, $total);
            $n++;
        }

        Console::endProgress("done." . PHP_EOL);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
    }
}
