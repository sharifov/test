<?php

namespace sales\logger\formatter;

use common\models\LeadPreferences;

/**
 * Class LeadPreferencesFormatter
 * @package sales\logger\formatter
 *
 * @property LeadPreferences $leadPreferences
 */
class LeadPreferencesFormatter implements Formatter
{
	/**
	 * @var LeadPreferences
	 */
	private $leadPreferences;

	/**
	 * LeadPreferencesFormatter constructor.
	 * @param LeadPreferences $leadPreferences
	 */
	public function __construct(LeadPreferences $leadPreferences)
	{
		$this->leadPreferences = $leadPreferences;
	}

	/**
	 * @param string $attribute
	 * @return string
	 */
	public function getFormattedAttributeLabel(string $attribute): string
	{
		return $this->leadPreferences->getAttributeLabel($attribute);
	}

	/**
	 * @param $attribute
	 * @param $value
	 * @return mixed
	 */
	public function getFormattedAttributeValue($attribute, $value)
	{
		return $value;
	}

	/**
	 * @return array
	 */
	public function getExceptedAttributes(): array
	{
		return [];
	}
}