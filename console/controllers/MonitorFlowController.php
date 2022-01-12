<?php

namespace console\controllers;

use common\models\Lead;
use common\models\Quote;
use src\helpers\app\AppHelper;
use src\repositories\lead\LeadRepository;
use yii\console\Controller;
use Yii;
use yii\db\Expression;
use yii\db\Query;
use yii\helpers\Console;
use yii\helpers\VarDumper;

/**
 * Class MonitorFlowController
 *
 * @property LeadRepository $leadRepository
 */
class MonitorFlowController extends Controller
{
    private $leadRepository;

    public function __construct($id, $module, LeadRepository $leadRepository, $config = [])
    {
        parent::__construct($id, $module, $config);
        $this->leadRepository = $leadRepository;
    }

    /**
     * @param bool $test
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function actionWatchDogDeclineQuote($test = false): void
    {
        printf("\n --- Start [" . date('Y-m-d H:i:s') . "] %s ---\n", $this->ansiFormat(self::class . '/' . $this->action->id, Console::FG_YELLOW));
        $time_start = microtime(true);

        $resultQuery = Yii::$app->db->createCommand(
            'SELECT 
                    `quotes`.`id`,
                    `quotes`.`uid`,
                    `quotes`.`lead_id`,
                    `quotes`.`created`
                FROM
                    `quotes`
                INNER JOIN
                    `leads` ON `quotes`.`lead_id` = `leads`.`id` AND `leads`.`status` NOT IN (:statusLeadBooked, :statusLeadSold, :statusLeadPending)
                
                LEFT JOIN (
                        SELECT 
                            `quote_applied`.`lead_id`
                        FROM
                            `quotes` AS `quote_applied`
                        WHERE 
                            `quote_applied`.`status` = :statusAppliedQuote
                        GROUP BY 
                            `quote_applied`.`lead_id`
                
                ) AS `lead_quote_applied`
                ON 
                    `lead_quote_applied`.`lead_id` = `quotes`.`lead_id`
                WHERE
                        `quotes`.`status` IN (:statusQuoteCreated, :statusQuoteSend, :statusQuoteOpen)
                    AND 
                        `quotes`.`created` <= :createdQuote
                    AND 
                       `lead_quote_applied`.`lead_id` IS NULL
                
                ORDER BY `quotes`.`id` DESC',
            [
                ':statusLeadBooked' => Lead::STATUS_BOOKED,
                ':statusLeadSold' => Lead::STATUS_SOLD,
                ':statusLeadPending' => Lead::STATUS_PENDING,
                ':statusAppliedQuote' => Quote::STATUS_APPLIED,
                ':statusQuoteCreated' => Quote::STATUS_CREATED,
                ':statusQuoteSend' => Quote::STATUS_SENT,
                ':statusQuoteOpen' => Quote::STATUS_OPENED,
                ':createdQuote' => date('Y-m-d H:i:s', strtotime('-1 day'))
            ]
        )
        ->queryAll();

        foreach ($resultQuery as $quoteData) {
            try {
                if (!Quote::updateAll(['status' => Quote::STATUS_DECLINED], ['id' => $quoteData['id']])) {
                    throw new \RuntimeException('Quote (' . $quoteData['id'] . ') not saved, status not changed to declined.');
                }
                if ($test) {
                    echo sprintf(
                        'Decline quote: %s. FR: %d, Created: %s',
                        $quoteData['uid'],
                        $quoteData['lead_id'],
                        $quoteData['created']
                    ) . PHP_EOL;
                }
            } catch (\Throwable $throwable) {
                $message = AppHelper::throwableLog($throwable);
                $message['data'] = $quoteData;
                Yii::error($message, 'console:MonitorFlowController:actionWatchDogDeclineQuote:Quote:save');
                echo 'Quote: ' . $quoteData['id'] . ' ' . VarDumper::dumpAsString($throwable->getMessage()) . PHP_EOL;
            }
        }

        if ($test) {
            echo sprintf('Count: %s', count($resultQuery)); // . PHP_EOL;
        }

        $time_end = microtime(true);
        $time = number_format(round($time_end - $time_start, 2), 2);

        Yii::info('Execute Time: ' . $time . ', count quotes: ' . count($resultQuery), 'Console:MonitorFlowController/watch-dog-decline-quote');
        printf("\nExecute Time: %s, count quotes: " . count($resultQuery), $this->ansiFormat($time . ' s', Console::FG_RED));
        printf("\n --- End [" . date('Y-m-d H:i:s') . "] %s ---\n", $this->ansiFormat(self::class . '/' . $this->action->id, Console::FG_YELLOW));
    }

    /**
     * @param bool $test
     */
    public function actionOnWake($test = false): void
    {
        printf("\n --- Start [" . date('Y-m-d H:i:s') . "] %s ---\n", $this->ansiFormat(self::class . '/' . $this->action->id, Console::FG_YELLOW));
        $time_start = microtime(true);

        /** @var Lead[] $leads */

        $leads = Lead::find()->where(['status' => Lead::STATUS_SNOOZE])->andWhere(['<=', 'snooze_for', date('Y-m-d H:i:s')])->limit(100)->all();

        if ($leads) {
            foreach ($leads as $lead) {
//                $lead->status = Lead::STATUS_PROCESSING;

                try {
                    $lead->processing($lead->employee_id, null, 'From Snooze to Processing. Wake.');
                    $lead->snooze_for = null;
                    $this->leadRepository->save($lead);
                } catch (\Throwable $e) {
                    Yii::error('Lead: ' . $lead->id . ', ' . VarDumper::dumpAsString($lead->errors), 'console:MonitorFlowController:actionOnWake:Lead:save');
                    if ($test) {
                        echo 'Lead: ' . $lead->id . ' ' . VarDumper::dumpAsString($lead->errors) . "\r\n";
                    }
                }
//                if ($lead->save()) {
//
//                } else {
//                    Yii::error('Lead: ' . $lead->id . ', ' . VarDumper::dumpAsString($lead->errors), 'console:MonitorFlowController:actionOnWake:Lead:save');
//                    if ($test) {
//                        echo 'Lead: ' . $lead->id . ' ' . VarDumper::dumpAsString($lead->errors) . "\r\n";
//                    }
//                }
            }
        }

        if ($test) {
            echo sprintf('Count: %s', count($leads)) . PHP_EOL;
        }

        $time_end = microtime(true);
        $time = number_format(round($time_end - $time_start, 2), 2);

        Yii::info('Execute Time: ' . $time . ', count leads: ' . ($leads ? count($leads) : 0), 'Console:MonitorFlowController/on-wake');
        printf("\nExecute Time: %s, count leads: " . count($leads), $this->ansiFormat($time . ' s', Console::FG_RED));
        printf("\n --- End [" . date('Y-m-d H:i:s') . "] %s ---\n", $this->ansiFormat(self::class . '/' . $this->action->id, Console::FG_YELLOW));
    }


    /**
     * @param bool $test
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function actionFollowUp($test = false): void
    {
        printf("\n --- Start [" . date('Y-m-d H:i:s') . "] %s ---\n", $this->ansiFormat(self::class . '/' . $this->action->id, Console::FG_YELLOW));
        $time_start = microtime(true);

        $query = Lead::find()->select(['id'])->where(['status' => [Lead::STATUS_PROCESSING, Lead::STATUS_ON_HOLD]])->andWhere(['<=', 'l_last_action_dt', date('Y-m-d H:i:s', strtotime('-48 hours'))]);

        //echo $query->createCommand()->getRawSql(); exit;

        /** @var Lead[] $leads */
        $leads = $query->all();

        if ($test) {
            echo sprintf('Count: %s', count($leads)) . PHP_EOL;
        }

        foreach ($leads as $lead) {
            $lead->status = $lead::STATUS_FOLLOW_UP;
            if ($lead->update(false)) {
                if ($test) {
                    echo $lead->id . ' - status: ' . $lead->status . PHP_EOL;
                }
            } else {
                Yii::error('Lead: ' . $lead->id . ', ' . VarDumper::dumpAsString($lead->errors), 'console:MonitorFlowController:actionFollowUp:Lead:save');
                if ($test) {
                    echo 'Lead: ' . $lead->id . ' ' . VarDumper::dumpAsString($lead->errors) . "\r\n";
                }
            }
        }

        if ($test) {
            echo sprintf('Count: %s', count($leads)) . PHP_EOL;
        }

        $time_end = microtime(true);
        $time = number_format(round($time_end - $time_start, 2), 2);

        Yii::info('Execute Time: ' . $time . ', count leads: ' . count($leads), 'Console:MonitorFlowController/follow-up');
        printf("\nExecute Time: %s, count leads: " . count($leads), $this->ansiFormat($time . ' s', Console::FG_RED));
        printf("\n --- End [" . date('Y-m-d H:i:s') . "] %s ---\n", $this->ansiFormat(self::class . '/' . $this->action->id, Console::FG_YELLOW));
    }
}
