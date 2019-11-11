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

	public function actionFormatLogManagedAttr(): void
	{
		$logs = GlobalLog::find()->where(['gl_formatted_attr' => null])->limit(16000)->all();

		foreach ($logs as $log) {
			$log->gl_formatted_attr = $this->globalLogFormatAttrService->formatAttr($log->gl_model, $log->gl_old_attr, $log->gl_new_attr);

			if (!empty($log->gl_formatted_attr)) {
				if (!$log->save()) {
					\Yii::error('Error while saving log: ' . $this->getParsedErrors($log->getErrors()), 'Console:LoggerController:actionFormatLogManagedAttr:GlobalLog:save');
				}
			} else if (!$log->delete()) {
				\Yii::error('Error while deleting log: ' . $this->getParsedErrors($log->getErrors()), 'Console:LoggerController:actionFormatLogManagedAttr:GlobalLog:save');
			}
		}
	}
}