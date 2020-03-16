<?php
/**
 * Created
 * User: alex.connor@techork.com
 * Date: 15/11/2019
 * Time: 11:05 AM
 */

namespace console\controllers;

use common\models\Currency;
use sales\helpers\app\AppHelper;
use sales\model\sms\entity\smsDistributionList\SmsDistributionList;
use yii\console\Controller;
use Yii;
use yii\helpers\Console;
use yii\helpers\VarDumper;

/**
 * App Service List
 *
 */
class ServiceController extends Controller
{

    /**
     *  Run update currency list & rates
     */
    public function actionUpdateCurrency(): void
    {
        printf("\n --- Start (" . date('H:i:s') .") %s ---\n", $this->ansiFormat(self::class . ' - ' . $this->action->id, Console::FG_YELLOW));

        try {
            $result = Currency::synchronization();

            if ($result) {
                if ($result['error']) {
                    Yii::error($result['error'], 'Console:ServiceController:actionUpdateCurrency:Throwable');
                    echo $this->ansiFormat('Error: ' . $result['error'], Console::FG_RED) . PHP_EOL;
                } else {
                    echo $this->ansiFormat('- Synchronization successful', Console::FG_BLUE) . PHP_EOL;
                    if ($result['created']) {
                        echo $this->ansiFormat('- Created currency: "' . implode(', ', $result['created']) . '"', Console::FG_YELLOW) . PHP_EOL;
                    }
                    if ($result['updated']) {
                        echo $this->ansiFormat('- Updated currency: "' . implode(', ', $result['updated']) . '"', Console::FG_GREEN) . PHP_EOL;
                    }

                }
            }
        } catch (\Throwable $throwable) {
            $message = AppHelper::throwableFormatter($throwable);
            Yii::error($message, 'Console:ServiceController:actionUpdateCurrency:Throwable');
            echo $this->ansiFormat('Error: ' . $message, Console::FG_RED) . PHP_EOL;
        }

        printf("\n --- End (" . date('H:i:s') .") %s ---\n", $this->ansiFormat(self::class . ' - ' . $this->action->id, Console::FG_YELLOW));
    }

    /**
     *  Send Sms from Distribution List
     */
    public function actionSendSms(): void
    {
        printf("\n --- Start (" . date('H:i:s') .") %s ---\n", $this->ansiFormat(self::class . ' - ' . $this->action->id, Console::FG_YELLOW));

        $count = Yii::$app->params['settings']['sms_distribution_count'] ?? 0;
        $n = 0;
        if ($count) {
            try {
                $smsList = SmsDistributionList::getSmsListForJob($count);
                if ($smsList) {
                   foreach ($smsList as $smsItem) {
                       $result = $smsItem->sendSms();
                       echo (++$n) . '. Id: ' . $smsItem->sdl_id . ' ';
                       echo VarDumper::dumpAsString($result) . PHP_EOL;
                   }
                } else {
                    echo $this->ansiFormat(' - SMS List is empty! -', Console::FG_RED);
                }
            } catch (\Throwable $throwable) {
                $message = AppHelper::throwableFormatter($throwable);
                Yii::error($message, 'Console:ServiceController:actionSendSmsDistributionList:Throwable');
                echo $this->ansiFormat('Error: ' . $message, Console::FG_RED) . PHP_EOL;
            }
        } else {
            printf("\n Setting %s is empty! \n", $this->ansiFormat('sms_distribution_count', Console::FG_YELLOW));
        }

        printf("\n --- End (" . date('H:i:s') .") %s ---\n", $this->ansiFormat(self::class . ' - ' . $this->action->id, Console::FG_YELLOW));
    }




}