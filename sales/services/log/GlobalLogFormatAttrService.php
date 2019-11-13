<?php


namespace sales\services\log;


use sales\logger\formatter\Formatter;
use yii\db\ActiveRecord;

class GlobalLogFormatAttrService
{
	/**
	 * @param string $modelPath
	 * @param string $oldAttr
	 * @param string $newAttr
	 * @return string|null
	 */
	public function formatAttr(string $modelPath, ?string $oldAttr, ?string $newAttr): ?string
	{
		$formattedAttr = [];

		try {
			$model = \Yii::createObject($modelPath);

			$formatterName = 'sales\\logger\\formatter\\' . (new \ReflectionClass($modelPath))->getShortName() . 'Formatter';

			if (class_exists($formatterName)) {
				$formatter = \Yii::createObject($formatterName);
				$this->formatByFormatter($formatter, $formattedAttr, $oldAttr, $newAttr);
			} else {
				$this->formatByModel($model, $formattedAttr, $oldAttr, $newAttr);
			}

			if (empty($formattedAttr)) {
				return null;
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
		if ($newAttr) {
			$oldAttr = json_decode($oldAttr, true);
			$newAttr = json_decode($newAttr, true);
			foreach ($newAttr as $attr => $value) {
				if (!in_array($attr, $formatter->getExceptedAttributes(), false)) {
					$formattedAttr[$formatter->getFormattedAttributeLabel($attr)][1] = $formatter->getFormattedAttributeValue($attr, $value);
					if (isset($oldAttr[$attr])) {
						$formattedAttr[$formatter->getFormattedAttributeLabel($attr)][0] = $formatter->getFormattedAttributeValue($attr, $oldAttr[$attr]);
					}
				}
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
		if ($newAttr) {
			$oldAttr = json_decode($oldAttr, true);
			$newAttr = json_decode($newAttr, true);
			foreach ($newAttr as $attr => $value) {
				$formattedAttr[$model->getAttributeLabel($attr)][1] = $value;
				if (isset($oldAttr[$attr])) {
					$formattedAttr[$model->getAttributeLabel($attr)][0] = $oldAttr[$attr];
				}
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