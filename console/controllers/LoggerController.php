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
 */
class LoggerController extends Controller
{
	/**
	 * @var Logger
	 */
	private $logger;

	/**
	 * LoggerController constructor.
	 * @param $id
	 * @param $module
	 * @param Logger $logger
	 * @param array $config
	 */
	public function __construct($id, $module, Logger $logger, $config = [])
	{
		parent::__construct($id, $module, $config);
		$this->logger = $logger;
	}

	public function actionFormatLogManagedAttr(): void
	{
		$logs = GlobalLog::find()->where(['gl_formatted_attr' => null])->limit(500)->all();

		foreach ($logs as $log) {
			$log->gl_formatted_attr = $this->formatAttr($log->gl_model, $log->gl_old_attr, $log->gl_new_attr);

			if (!$log->save()) {
				\Yii::error('Error while saving log: ' . $this->getParsedErrors($log->getErrors()), 'Console:LoggerController:actionFormatLogManagedAttr:GlobalLog:save');
			}
		}
	}

	/**
	 * @param string $modelPath
	 * @param string $oldAttr
	 * @param string $newAttr
	 * @return string|null
	 */
	private function formatAttr(string $modelPath, ?string $oldAttr, ?string $newAttr): ?string
	{
		$formattedAttr = [
			'old' => [],
			'new' => []
		];

		try {
			$model = \Yii::createObject($modelPath);

			$formatterName = 'sales\\logger\\formatter\\' . (new \ReflectionClass($modelPath))->getShortName() . 'Formatter';

			if (class_exists($formatterName)) {
				$formatter = \Yii::createObject($formatterName);
				$this->formatByFormatter($formatter, $formattedAttr, $oldAttr, $newAttr);
			} else {
				$this->formatByModel($model, $formattedAttr, $oldAttr, $newAttr);
			}

			return json_encode($formattedAttr);
		} catch (\Throwable $e) {
			\Yii::error($e->getMessage() . ' File: ' . $e->getFile() . ' Line: ' . $e->getLine(), 'Console:LoggerController:formAttr:Throwable');

			return null;
		}
	}

	/**
	 * @param Formatter $formatter
	 * @param array $formattedAttr
	 * @param string|null $oldAttr
	 * @param string|null $newAttr
	 */
	private function formatByFormatter(Formatter $formatter, array &$formattedAttr, ?string $oldAttr, ?string $newAttr): void
	{
		if ($oldAttr) {
			$oldAttr = json_decode($oldAttr, true);
			foreach ($oldAttr as $attr => $value) {
				$formattedAttr['old'][$formatter->getFormattedAttributeLabel($attr)] = $formatter->getFormattedAttributeValue($attr, $value);
			}
		}

		if ($newAttr) {
			$newAttr = json_decode($newAttr, true);
			foreach ($newAttr as $attr => $value) {
				$formattedAttr['new'][$formatter->getFormattedAttributeLabel($attr)] = $formatter->getFormattedAttributeValue($attr, $value);
			}
		}
	}

	/**
	 * @param ActiveRecord $model
	 * @param array $formattedAttr
	 * @param string|null $oldAttr
	 * @param string|null $newAttr
	 */
	private function formatByModel(ActiveRecord $model, array &$formattedAttr, ?string $oldAttr, ?string $newAttr): void
	{
		if ($oldAttr) {
			$oldAttr = json_decode($oldAttr, true);
			foreach ($oldAttr as $attr => $value) {
				$formattedAttr['old'][$model->getAttributeLabel($attr)] = $value;
			}
		}

		if ($newAttr) {
			$newAttr = json_decode($newAttr, true);
			foreach ($newAttr as $attr => $value) {
				$formattedAttr['new'][$model->getAttributeLabel($attr)] = $value;
			}
		}
	}

	/**
	 * @param array $errors
	 * @return string
	 */
	private function getParsedErrors(array $errors): string
	{
		return implode('<br>', array_map(static function ($errors) {
			return implode('<br>', $errors);
		}, $errors));
	}
}