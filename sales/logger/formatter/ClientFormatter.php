<?php

namespace sales\logger\formatter;

use common\models\Client;

/**
 * Class ClientFormatter
 * @package sales\logger\formatter
 *
 * @property Client $client
 */
class ClientFormatter implements Formatter
{
	/**
	 * @var Client
	 */
	private $client;

	/**
	 * ClientFormatter constructor.
	 * @param Client $client
	 */
	public function __construct(Client $client)
	{
		$this->client = $client;
	}

	/**
	 * @param string $attribute
	 * @return string
	 */
	public function getFormattedAttributeLabel(string $attribute): string
	{
		return $this->client->getAttributeLabel($attribute);
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
		return [
			'updated',
			'created'
		];
	}
}