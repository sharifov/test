<?php


namespace console\controllers;

use common\bootstrap\Logger;
use sales\entities\log\GlobalLog;
use yii\console\Controller;
use yii\db\ActiveRecord;

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

			if ($oldAttr) {
				$oldAttr = json_decode($oldAttr, true);
				foreach ($oldAttr as $attr => $value) {
					$formattedAttr['old'][$model->getAttributeLabel($attr)] = $this->formatAttrValue($model, $attr, $value);
//					$formattedAttr['old'][$model->getAttributeLabel($attr)] = $value;
				}
			}

			if ($newAttr) {
				$newAttr = json_decode($newAttr, true);
				foreach ($newAttr as $attr => $value) {
					$formattedAttr['new'][$model->getAttributeLabel($attr)] = $this->formatAttrValue($model, $attr, $value);
//					$formattedAttr['new'][$model->getAttributeLabel($attr)] = $value;
				}
			}

			return json_encode($formattedAttr);
		} catch (\Throwable $e) {
			\Yii::error($e->getMessage(), 'Console:LoggerController:formAttr:Throwable');

			return null;
		}
	}

	/**
	 * @param ActiveRecord $model
	 * @param $attr
	 * @param $value
	 * @return mixed
	 */
	private function formatAttrValue(ActiveRecord $model, $attr, $value)
	{
		$functions = $model->formatValue();

		if (array_key_exists($attr, $functions)) {
			return $functions[$attr]($value);
		}

		return $value;
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