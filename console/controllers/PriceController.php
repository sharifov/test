<?php

namespace console\controllers;

use common\models\Call;
use common\models\Sms;
use console\helpers\OutputHelper;
use sales\model\call\useCase\UpdateCallPrice;
use sales\model\sms\useCase\UpdateSmsPrice;
use yii\console\Controller;
use yii\helpers\Console;

/**
 * Class PriceController
 *
 * @property UpdateCallPrice $updateCallPrice
 * @property UpdateSmsPrice $updateSmsPrice
 * @property OutputHelper $outputHelper
 */
class PriceController extends Controller
{
    private UpdateCallPrice $updateCallPrice;
    private OutputHelper $outputHelper;
    private UpdateSmsPrice $updateSmsPrice;

    public function __construct($id, $module, UpdateCallPrice $updateCallPrice, UpdateSmsPrice $updateSmsPrice, OutputHelper $outputHelper, $config = [])
    {
        parent::__construct($id, $module, $config);
        $this->updateCallPrice = $updateCallPrice;
        $this->outputHelper = $outputHelper;
        $this->updateSmsPrice = $updateSmsPrice;
    }

    public function actionUpdateCallsPrice()
    {
        $point = OutputHelper::getShortClassName(self::class) . ':' . $this->action->id;
        $this->outputHelper->printInfo('Start. ', $point);
        $start = time();

        $date = (new \DateTimeImmutable())->modify('- 1 hour');
        $limit = 100;

        $calls = Call::find()
            ->andWhere(['c_call_status' => Call::TW_STATUS_COMPLETED])
            ->andWhere(['IS', 'c_price', null])
            ->andWhere(['<', 'c_updated_dt', $date->format('Y-m-d H:i:s')])
            ->orderBy(['c_id' => SORT_DESC])
            ->limit($limit)
            ->asArray()
            ->all();

        $this->outputHelper->printInfo('Count calls for update', count($calls));

        foreach ($calls as $call) {
            try {
                $this->updateCallPrice->update($call['c_call_sid']);
                printf('%s', $this->ansiFormat('.', Console::FG_GREEN));
            } catch (\Throwable $e) {
                printf('%s', $this->ansiFormat('.', Console::FG_RED));
//                \Yii::error($e->getMessage(), 'PriceController:actionUpdateCallsPrice:update');
            }
        }

        $this->outputHelper->printInfo('End. Duration: ' . (time() - $start) . ' sec', $point);
    }

    public function actionUpdateSmsesPrice()
    {
        $point = OutputHelper::getShortClassName(self::class) . ':' . $this->action->id;
        $this->outputHelper->printInfo('Start. ', $point);
        $start = time();

        $date = (new \DateTimeImmutable())->modify('- 1 hour');
        $limit = 100;

        $smses = Sms::find()
            ->andWhere(['IS NOT', 's_tw_message_sid', null])
            ->andWhere(['s_status_id' => [Sms::STATUS_DONE, Sms::STATUS_CANCEL, Sms::STATUS_ERROR]])
            ->andWhere(['IS', 's_tw_price', null])
            ->andWhere(['<', 's_updated_dt', $date->format('Y-m-d H:i:s')])
            ->orderBy(['s_id' => SORT_DESC])
            ->limit($limit)
            ->asArray()
            ->all();

        $this->outputHelper->printInfo('Count sms for update', count($smses));

        foreach ($smses as $sms) {
            try {
                $this->updateSmsPrice->update($sms['s_tw_message_sid']);
                printf('%s', $this->ansiFormat('.', Console::FG_GREEN));
            } catch (\Throwable $e) {
                printf('%s', $this->ansiFormat('.', Console::FG_RED));
//                \Yii::error($e->getMessage(), 'PriceController:actionUpdateSmsPrice:update');
            }
        }

        $this->outputHelper->printInfo('End. Duration: ' . (time() - $start) . ' sec', $point);
    }
}
