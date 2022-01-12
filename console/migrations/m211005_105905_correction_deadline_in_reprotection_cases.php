<?php

use modules\product\src\entities\productQuote\ProductQuote;
use src\entities\cases\Cases;
use src\helpers\app\AppHelper;
use src\helpers\ErrorsToStringHelper;
use src\helpers\setting\SettingHelper;
use yii\db\Migration;

/**
 * Class m211005_105905_correction_deadline_in_reprotection_cases
 */
class m211005_105905_correction_deadline_in_reprotection_cases extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $sql = '
            SELECT 
                cs_id AS case_id,
                MAX(pq_id) AS last_reprotection_quote_id
            FROM
                case_status_log
                    INNER JOIN
                cases ON cs_id = csl_case_id
                    AND cs_status NOT IN (10 , 11)
                    INNER JOIN
                case_category ON cc_id = cs_category_id
                    AND cc_key = \'flight_schedule_change\'
                    INNER JOIN
                product_quote_change ON pqc_case_id = cs_id
                    INNER JOIN
                product_quote_relation ON pqr_parent_pq_id = pqc_pq_id
                    AND pqr_type_id = 4
                    INNER JOIN
                product_quote ON pq_id = pqr_related_pq_id
                    AND pq_status_id NOT IN (10 , 11)
            WHERE
                csl_to_status = 5
            GROUP BY cs_id
        ';

        $result = Yii::$app->db->createCommand($sql)->queryAll();

        foreach ($result as $value) {
            $pQuoteId = $value['last_reprotection_quote_id'];
            if (($productQuote = ProductQuote::findOne($pQuoteId)) && ($flightQuote = $productQuote->flightQuote)) {
                try {
                    foreach ($flightQuote->flightQuoteTrips as $key => $trip) {
                        if (!(($firstSegment = $trip->flightQuoteSegments[0]) && $firstSegment->fqs_departure_dt)) {
                            throw new \RuntimeException('Deadline not created. Reason - Segments departure not correct. PQ(' . $pQuoteId . ')');
                        }
                        $curTime = new \DateTime('now', new \DateTimeZone('UTC'));
                        $departureTime = new \DateTime($firstSegment->fqs_departure_dt, new \DateTimeZone('UTC'));

                        if ($curTime <= $departureTime && $case = Cases::findOne($value['case_id'])) {
                            $schdCaseDeadlineHours = SettingHelper::getSchdCaseDeadlineHours();
                            $deadline = $departureTime->modify(' -' . $schdCaseDeadlineHours . ' hours')->format('Y-m-d H:i:s');
                            $oldDeadline = $case->cs_deadline_dt;
                            $case->cs_deadline_dt = $deadline;
                            if (!$case->save()) {
                                throw new \RuntimeException(ErrorsToStringHelper::extractFromModel($case));
                            }
                            echo 'Case ' . $value['case_id'] . ' deadline is corrected. Old(' . $oldDeadline . ') New(' . $deadline . ')' . PHP_EOL;
                            continue;
                        }
                    }
                } catch (\Throwable $throwable) {
                    Yii::error(AppHelper::throwableLog($throwable), 'correction_deadline_in_reprotection_cases:Throwable');
                }
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m211005_105905_correction_deadline_in_reprotection_cases cannot be reverted.\n";
    }
}
