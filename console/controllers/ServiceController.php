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
use yii\console\Controller;
use Yii;
use yii\helpers\Console;

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

}