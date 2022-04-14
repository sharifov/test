<?php

namespace console\controllers;

use src\entities\cases\Cases;
use src\entities\cases\CasesStatus;
use src\helpers\app\AppHelper;
use src\repositories\cases\CasesRepository;
use Yii;
use yii\console\Controller;
use yii\helpers\Console;

/**
 * Class CaseController
 *
 * @property CasesRepository $caseRepository
 */
class CaseTestController extends Controller
{
    private $caseRepository;

    public function __construct($id, $module, CasesRepository $caseRepository, $config = [])
    {
        parent::__construct($id, $module, $config);
        $this->caseRepository = $caseRepository;
    }


    public function actionCheckSwitchStatusSolved()
    {
        printf("\n --- Start %s ---\n", $this->ansiFormat(self::class . ' - ' . $this->action->id, Console::FG_YELLOW));
        $time_start = microtime(true);

        $currentTime = (new \DateTime('now', new \DateTimeZone('UTC')))->format('Y-m-d H:i:s');

        echo Console::renderColoredString('%g --- CurrentTime: %w[' . $currentTime . '] %n'), PHP_EOL;

        $case = Cases::find()->where(['cs_status' => CasesStatus::STATUS_AWAITING])->one();

        if ($case) {
            try {
                $case->solved($case->cs_user_id, 'test status notification solved');
                $this->caseRepository->save($case);
            } catch (\Throwable $e) {
                $message = AppHelper::throwableLog($e);
                $message['caseId'] = $case->cs_id;
                Yii::error($message, 'console:CaseController:actionCheckSwitchStatusSolved:Case:save');
            }
        }

        $time_end = microtime(true);
        $time = number_format(round($time_end - $time_start, 2), 2);
        printf("\n --- Execute Time: %s ", $this->ansiFormat($time . ' s', Console::FG_RED));
        printf("\n --- End %s ---\n", $this->ansiFormat(self::class . ' - ' . $this->action->id, Console::FG_YELLOW));
    }

    public function actionCheckSwitchStatusError()
    {
        printf("\n --- Start %s ---\n", $this->ansiFormat(self::class . ' - ' . $this->action->id, Console::FG_YELLOW));
        $time_start = microtime(true);

        $currentTime = (new \DateTime('now', new \DateTimeZone('UTC')))->format('Y-m-d H:i:s');

        echo Console::renderColoredString('%g --- CurrentTime: %w[' . $currentTime . '] %n'), PHP_EOL;

        $case = Cases::find()->where(['cs_status' => CasesStatus::STATUS_AWAITING])->one();

        if ($case) {
            try {
                $case->error($case->cs_user_id, 'test status notification error');
                $this->caseRepository->save($case);
            } catch (\Throwable $e) {
                $message = AppHelper::throwableLog($e);
                $message['caseId'] = $case->cs_id;
                Yii::error($message, 'console:CaseController:actionCheckSwitchStatusError:Case:save');
            }
        }

        $time_end = microtime(true);
        $time = number_format(round($time_end - $time_start, 2), 2);
        printf("\n --- Execute Time: %s ", $this->ansiFormat($time . ' s', Console::FG_RED));
        printf("\n --- End %s ---\n", $this->ansiFormat(self::class . ' - ' . $this->action->id, Console::FG_YELLOW));
    }
}
