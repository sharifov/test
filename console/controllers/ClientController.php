<?php

namespace console\controllers;

use common\models\Client;
use modules\objectSegment\src\contracts\ObjectSegmentKeyContract;
use modules\objectSegment\src\object\dto\ClientSegmentObjectDto;
use src\helpers\app\AppHelper;
use src\model\clientData\entity\ClientData;
use yii\console\Controller;
use yii\helpers\Console;

class ClientController extends Controller
{
    public function actionClientReturnIndication($limit = 1000)
    {
        echo Console::renderColoredString('%g --- Start %w[' . date('Y-m-d H:i:s') . '] %g' .
            self::class . ':' . __FUNCTION__ . ' %n'), PHP_EOL;

        $timeStart = microtime(true);

        $clients = Client::find()
            ->join('LEFT OUTER JOIN', ClientData::tableName(), 'cd_client_id = id')
            ->where(['cd_client_id' => null])
            ->orderBy(['id' => SORT_DESC])
            ->limit($limit)
            ->all();

        $processed = 0;
        $countItems = count($clients);

        \Yii::info('Script Started', 'info\console:ClientController:actionClientReturnIndication');
        if (!$countItems) {
            echo Console::renderColoredString('%g --- End: All types of client returns detected %w') . PHP_EOL;
            \Yii::info('All types of client returns detected', 'info\console:ClientController:actionClientReturnIndication');
            return;
        }

        Console::startProgress($processed, $countItems, 'Counting objects: ', false);

        foreach ($clients as $client) {
            try {
                $dto = new ClientSegmentObjectDto($client);
                \Yii::$app->objectSegment->segment($dto, ObjectSegmentKeyContract::TYPE_KEY_CLIENT);
            } catch (\Throwable $e) {
                \Yii::error(AppHelper::throwableLog($e, true), 'console::ClientReturnIndicationController::Throwable');
            }
            $processed++;
            Console::updateProgress($processed, $countItems);
        }
        Console::endProgress(false);
        \Yii::info([
            'processed' =>  $processed,
            'countItems' => $countItems
        ], 'info\console:ClientController:actionClientReturnIndication');

        $timeEnd = microtime(true);
        $time = number_format(round($timeEnd - $timeStart, 2), 2);
        echo Console::renderColoredString('%g --- Execute Time: %w[' . $time . ' s] %g') . PHP_EOL;
    }
}
