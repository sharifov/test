<?php

namespace sales\logger\formatter;

use common\models\ClientPhone;

/**
 * Class ClientPhoneFormatter
 * @package sales\logger\formatter
 *
 * @property ClientPhone $clientPhone
 */
class ClientPhoneFormatter implements Formatter
{
	/**
	 * @var ClientPhone
	 */
	private $clientPhone;

	/**
	 * ClientPhoneFormatter constructor.
	 * @param ClientPhone $clientPhone
	 */
	public function __construct(ClientPhone $clientPhone)
	{

		$this->clientPhone = $clientPhone;
	}

	/**
	 * @param string $attribute
	 * @return string
	 */
	public function getFormattedAttributeLabel(string $attribute): string
	{
		return $this->clientPhone->getAttributeLabel($attribute);
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
		$clientPhone = $this->clientPhone;
		return [
			'type' => static function ($value) use ($clientPhone) {
				return $clientPhone::PHONE_TYPE[$value];
			}
		];
	}
}