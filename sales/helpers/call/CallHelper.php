<?php
namespace sales\helpers\call;


use DateTime;
use yii\bootstrap4\Dropdown;
use yii\helpers\Html;

class CallHelper
{
	/**
	 * @param string $phone
	 * @param bool $access
	 * @param string|null $title
	 * @param array $dataParams
	 * @param string|null $tag
	 * @return string
	 */
	public static function callNumber(string $phone, bool $access, string $title = '', array $dataParams = [], ?string $tag = 'span'): string
	{
		$title = $title ?: $phone;

		$options = [
			'data-phone-number' => $phone,
			'data-confirm' => isset($dataParams['confirm']) ? 1 : 0,
			'data-call' => isset($dataParams['call']) ? 1 : 0,
			'class' => $access ? 'wg-call badge badge-pill badge-light' : ''
		];

		if (!empty($dataParams['data-title'])) {
		    $options['data-title'] = $dataParams['data-title'];
        }

		if (!empty($dataParams['phone-from-id'])) {
            $options['data-phone-from-id'] = $dataParams['phone-from-id'];
        }


        $disableIcon = $dataParams['disable-icon'] ?? false;
        if ($disableIcon) {
            return Html::tag($tag, $title, $options);
        }

		$iconClass = $dataParams['icon-class'] ?? 'fa fa-phone';
		$iconTag = Html::tag('i', '', [
			'class' => $iconClass
		]);

		return Html::tag($tag, $iconTag . ' ' . $title, $options);
	}

	/**
	 * @param array $phoneNumbers
	 * @param string|null $dropdownBtnContent
	 * @param bool $access
	 * @param array $buttonOptions
	 * @return string
	 * @throws \Exception
	 */
	public static function callNumbersDropdownList(array $phoneNumbers, ?string $dropdownBtnContent, bool $access, $buttonOptions = []): string
	{
		$dropdownBtnContent = $dropdownBtnContent ?? '<i class="fa fa-phone"></i> Phone List';
		$dropdownBtn = Html::tag('button', $dropdownBtnContent, [
			'class' => 'btn dropdown-toggle ' . ($buttonOptions['class'] ?? 'btn-secondary'),
			'type' => 'button',
			'data-toggle' => 'dropdown',
			'aria-haspopup' => 'true',
			'aria-expanded' => 'false'
		]);

		$numbers = [];
		foreach ($phoneNumbers as $phoneNumber) {
			$numbers = [
				'label' => self::callNumber(
					$phoneNumber['phone'] ?? '',
					$access,
					$phoneNumber['title'] ?? '',
					$phoneNumbers['dataParams'] ?? []
				),
				'encode' => false
			];
		}

		$widget = Dropdown::widget([
			'items' => [
				$numbers
			],
		]);

		return Html::tag('div', $dropdownBtn . $widget, ['class' => 'dropdown']);
	}

	public static function formatCallHistoryByDate(array $callHistory): array
	{
		$result = [
			'Today' => [],
			'Yesterday' => [],
		];

		foreach ($callHistory as $call) {
			$currentDate = new DateTime();
			$callDate = new DateTime($call['cl_call_created_dt']);
			$dDiff = $currentDate->diff($callDate);


			if ($dDiff->d === 0 && $dDiff->m === 0) {
				$result['Today'][] = $call;
			} elseif ($dDiff->d === 1 && $dDiff->m === 0) {
				$result['Yesterday'][] = $call;
			} else {
				$result[date('Y-m-d', strtotime($call['cl_call_created_dt']))][] = $call;
			}
		}

		return $result;
	}
}