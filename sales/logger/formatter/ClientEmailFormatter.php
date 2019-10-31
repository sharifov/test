<?php

namespace sales\logger\formatter;

use common\models\ClientEmail;

/**
 * Class ClientEmailFormatter
 * @package sales\logger\formatter
 *
 * @property ClientEmail $clientEmail
 */
class ClientEmailFormatter implements Formatter
{
	/**
	 * @var ClientEmail
	 */
	private $clientEmail;

	/**
	 * ClientEmailFormatter constructor.
	 * @param ClientEmail $clientEmail
	 */
	public function __construct(ClientEmail $clientEmail)
	{
		$this->clientEmail = $clientEmail;
	}

	/**
	 * @param string $attribute
	 * @return string
	 */
	public function getFormattedAttributeLabel(string $attribute): string
	{
		return $this->clientEmail->getAttributeLabel($attribute);
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
		$clientEmail = $this->clientEmail;
		return [
			'email' => static function ($value) use ($clientEmail) {
				return '<i class="fa fa-envelope"></i> ' . $value;
			},
			'type' => static function ($value) use ($clientEmail) {
				return $clientEmail::getEmailTypeLabel($value) ?? $value;
			}
		];
	}
}