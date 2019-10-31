<?php

namespace sales\logger\formatter;

use common\models\Lead;

/**
 * Class LeadFormatter
 * @package sales\logger\formatter
 *
 * @property Lead $lead
 */
class LeadFormatter implements Formatter
{
	/**
	 * @var Lead
	 */
	private $lead;

	/**
	 * LeadFormatter constructor.
	 * @param Lead $lead
	 */
	public function __construct(Lead $lead)
	{
		$this->lead = $lead;
	}

	/**
	 * @param string $attribute
	 * @return string
	 */
	public function getFormattedAttributeLabel(string $attribute): string
	{
		return $this->lead->getAttributeLabel($attribute);
	}

	/**
	 * @param $attribute
	 * @param $value
	 * @return mixed
	 */
	public function getFormattedAttributeValue($attribute, $value)
	{
		$functions = $this->getAttributeFormatters();

		if (array_key_exists($attribute, $functions)) {
			return $functions[$attribute]($value);
		}

		return $value;
	}

	/**
	 * @return array
	 */
	private function getAttributeFormatters(): array
	{
		$lead = $this->lead;
		return [
			'status' => static function ($value) use ($lead) {
				return $lead::STATUS_LIST[$value] ?? $value;
			},
			'l_call_status_id' => static function ($value) use ($lead) {
				return $lead::CALL_STATUS_LIST[$value] ?? $value;
			},
			'l_delayed_charge' => static function ($value) use ($lead) {
				if ($value) {
					return '<span class="label label-success">Yes</span>';
				}
				return '<span class="label label-danger">No</span>';
			}
		];
	}
}