<?php
namespace sales\helpers\call;


use yii\helpers\Html;

class CallHelper
{
	/**
	 * @param string $phone
	 * @param string|null $title
	 * @param array $dataParams
	 * @param string|null $tag
	 * @return string
	 */
	public static function callNumber(string $phone, string $title = '', array $dataParams = [], ?string $tag = 'span'): string
	{
		$title = $title ?: $phone;

		$options = [
			'data-phone-number' => $phone,
			'data-confirm' => isset($dataParams['confirm']) ? 1 : 0,
			'data-call' => isset($dataParams['call']) ? 1 : 0,
			'data-phone-from-id' => $dataParams['phone-from-id'] ?? '',
			'class' => 'wg-call'
		];

		$iconClass = $dataParams['icon-class'] ?? 'fa fa-phone';
		$iconTag = Html::tag('i', '', [
			'class' => $iconClass
		]);

		return $iconTag . ' ' . Html::tag($tag, $title, $options);
	}
}