<?php


namespace console\controllers;

use common\bootstrap\Logger;
use common\models\Client;
use common\models\ClientEmail;
use common\models\ClientPhone;
use common\models\Lead;
use common\models\LeadPreferences;
use sales\entities\log\GlobalLog;
use sales\logger\formatter\Formatter;
use sales\services\log\GlobalLogFormatAttrService;
use yii\base\InvalidConfigException;
use yii\console\Controller;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\db\Query;
use yii\helpers\Console;
use yii\helpers\VarDumper;

/**
 * Class LoggerController
 * @package console\controllers
 *
 * @property $logger Logger
 * @property GlobalLogFormatAttrService $globalLogFormatAttrService
 */
class LoggerController extends Controller
{
	/**
	 * @var Logger
	 */
	private $logger;
	/**
	 * @var GlobalLogFormatAttrService
	 */
	private $globalLogFormatAttrService;

	/**
	 * LoggerController constructor.
	 * @param $id
	 * @param $module
	 * @param Logger $logger
	 * @param GlobalLogFormatAttrService $globalLogFormatAttrService
	 * @param array $config
	 */
	public function __construct($id, $module, Logger $logger, GlobalLogFormatAttrService $globalLogFormatAttrService, $config = [])
	{
		parent::__construct($id, $module, $config);
		$this->logger = $logger;
		$this->globalLogFormatAttrService = $globalLogFormatAttrService;
	}

	public function actionFormatLogManagedAttr($limit = 1000, $countDays = 90): void
	{
		printf("\n --- Start %s ---\n", $this->ansiFormat(self::class . ' - ' . $this->action->id, Console::FG_YELLOW));
		$time_start = microtime(true);

		$logs = GlobalLog::find()->select(['gl_id', 'gl_formatted_attr', 'gl_model', 'gl_old_attr', 'gl_new_attr'])->where(['gl_formatted_attr' => null])->andWhere(['<=', 'ABS(TIMESTAMPDIFF(DAY, curdate(), gl_created_at))', $countDays])->limit($limit)->orderBy(['gl_id' => SORT_DESC])->all();

		foreach ($logs as $log) {
			$log->gl_formatted_attr = $this->globalLogFormatAttrService->formatAttr($log->gl_model, $log->gl_old_attr, $log->gl_new_attr);

			echo '.';

			if (!empty($log->gl_formatted_attr)) {
				if (!$log->save()) {
					\Yii::error('Error: ' . VarDumper::dumpAsString($log->errors), 'Console:LoggerController:actionFormatLogManagedAttr:GlobalLog:save');
				}
			} else if (!$log->delete()) {
				\Yii::error('Error while deleting log: ' . $this->getParsedErrors($log->getErrors()), 'Console:LoggerController:actionFormatLogManagedAttr:GlobalLog:save');
			}
		}
		$time_end = microtime(true);
		$time = number_format(round($time_end - $time_start, 2), 2);
		printf("\nExecute Time: %s, count Old Logs: " . count($logs), $this->ansiFormat($time . ' s', Console::FG_RED));
		printf("\n --- End %s ---\n", $this->ansiFormat(self::class . ' - ' . $this->action->id, Console::FG_YELLOW));
	}

	public function actionRemoveOldGlobalLogs(int $limit = 2000, int $countDays = 90)
	{
		if ($limit <= 0) {
			printf($this->ansiFormat("\n Error: The limit must be greater than: %s \n", Console::FG_RED), $this->ansiFormat($limit, Console::FG_YELLOW));
			exit;
		}
		if ($countDays <= 0) {
			printf($this->ansiFormat("\n Error: The count days must be greater than: %s \n", Console::FG_RED), $this->ansiFormat($countDays, Console::FG_YELLOW));
			exit;
		}

		printf("\n --- Start %s ---\n", $this->ansiFormat(self::class . ' - ' . $this->action->id, Console::FG_YELLOW));
		$time_start = microtime(true);
		$db = \Yii::$app->db;

		$maxGlId = $db->createCommand('Select max(gl_id) as gl_id from global_log where ABS(TIMESTAMPDIFF(DAY, curdate(), gl_created_at)) >= :days')->bindValue(':days', $countDays)->queryOne();

		$count = $db->createCommand('Select count(gl_id) as count_gl_id from global_log where ABS(TIMESTAMPDIFF(DAY, curdate(), gl_created_at)) >= :days')->bindValue(':days', $countDays)->queryOne();

		$iter = (int)(($count['count_gl_id']) / $limit);

		if ($count['count_gl_id']) {
			for ($i = 0; $i <= $iter; $i++) {
				$remain = abs($count['count_gl_id'] - $i * $limit);
				echo 'Removed logs: ' . $i * $limit . '; Remains: ' . $remain .  PHP_EOL;
				$db->createCommand('DELETE from global_log where gl_id <= :gl_id limit :limit')->bindValues([':gl_id' => (int)$maxGlId['gl_id'], ':limit' => $limit])->execute();
			}
		}

		$time_end = microtime(true);
		$time = number_format(round($time_end - $time_start, 2), 2);
		printf("\nExecute Time: %s, Count of removed old logs: " . $count['count_gl_id'], $this->ansiFormat($time . ' s', Console::FG_RED));
		printf("\n --- End %s ---\n", $this->ansiFormat(self::class . ' - ' . $this->action->id, Console::FG_YELLOW));
	}
}