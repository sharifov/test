<?php

namespace console\controllers;

use common\models\Lead;
use common\models\Quote;
use sales\repositories\lead\LeadRepository;
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

        $query = new Query();
        $subQuery = $query->select(['COUNT(*)'])->from('quotes AS q')->where(['q.status' => Quote::STATUS_APPLIED])->andWhere('quotes.lead_id = q.lead_id')->createCommand()->getRawSql();

        $quotes = Quote::find()->select(['quotes.id', 'quotes.uid', 'quotes.lead_id', 'quotes.created'])
            ->joinWith('lead')
            ->where(['quotes.status' => [Quote::STATUS_CREATED, Quote::STATUS_SEND, Quote::STATUS_OPENED]])
            ->andWhere(['<=', 'quotes.created', date('Y-m-d H:i:s', strtotime('-1 day'))])
            ->andWhere(['NOT IN', 'leads.status', [Lead::STATUS_BOOKED, Lead::STATUS_SOLD, Lead::STATUS_PENDING]])
            //->andWhere('(SELECT COUNT(*) FROM quotes AS q WHERE quotes.lead_id = q.lead_id AND q.status = 2) < 1')
            ->andWhere(['<', new Expression('(' . $subQuery . ')'), 1])
            ->orderBy(['quotes.id' => SORT_DESC])
            //->limit(500)
            ->all();


        //echo $quotes->createCommand()->getRawSql(); exit;

        if($quotes) {
            foreach ($quotes as $quote) {

                /* $existQuote = Quote::find()->where(['lead_id' => $quote->lead_id, 'status' => Quote::STATUS_APPLIED])->exists();
                if ($existQuote) {
                    if ($test) {
                        echo 'Exist alternative applied quote Lead ' . $quote->lead_id . "\r\n";
                    }
                    continue;
                }*/


                $quote->status = Quote::STATUS_DECLINED;

                if ($quote->update(false)) {
                    if ($test) {
                        echo sprintf('Decline quote: %s. FR: %d, Created: %s', $quote->uid, $quote->lead_id, $quote->created) . PHP_EOL;
                    }
                } else {
                    Yii::error('Quote: ' . $quote->id . ', ' . VarDumper::dumpAsString($quote->errors), 'console:MonitorFlowController:actionWatchDogDeclineQuote:Quote:save');
                    if ($test) {
                        echo 'Quote: ' . $quote->id . ' ' . VarDumper::dumpAsString($quote->errors) . "\r\n";
                    }
                }
            }
        }

        if ($test) {
            echo sprintf('Count: %s', count($quotes)); // . PHP_EOL;
        }

        $time_end = microtime(true);
        $time = number_format(round($time_end - $time_start, 2), 2);

        Yii::info('Execute Time: ' . $time . ', count leads: ' . count($quotes), 'Console:MonitorFlowController/watch-dog-decline-quote');
        printf("\nExecute Time: %s, count leads: " . count($quotes), $this->ansiFormat($time . ' s', Console::FG_RED));
        printf("\n --- End [" . date('Y-m-d H:i:s') . "] %s ---\n", $this->ansiFormat(self::class . '/' . $this->action->id, Console::FG_YELLOW));
    }

    /**
     * @param bool $test
     */
    public function actionOnWake($test = false) : void
    {
        printf("\n --- Start [" . date('Y-m-d H:i:s') . "] %s ---\n", $this->ansiFormat(self::class . '/' . $this->action->id, Console::FG_YELLOW));
        $time_start = microtime(true);

        /** @var Lead[] $leads */

        $leads = Lead::find()->where(['status' => Lead::STATUS_SNOOZE])->andWhere(['<=', 'snooze_for', date('Y-m-d H:i:s')])->limit(100)->all();

        if($leads) {
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