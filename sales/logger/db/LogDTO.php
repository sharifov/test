<?php


namespace sales\logger\db;

/**
 * Class LogDTO
 * @package sales\services\logs
 *
 * @property $glModel
 * @property $glObjectId
 * @property $glAppId
 * @property $glAppUserId
 * @property $glOldAttr
 * @property $glNewAttr
 * @property $glFormattedAttr
 */
class LogDTO
{
	public $glModel;
	public $glObjectId;
	public $glAppId;
	public $glAppUserId;
	public $glOldAttr;
	public $glNewAttr;
	public $glFormattedAttr;

	public function __construct(
		string $glModel,
		int $glObjectId,
		string $glAppId,
		?int $glAppUserId = null,
		?string $glOldAttr = null,
		?string $glNewAttr = null,
		?string $glFormattedAttr = null
	)
	{

		$this->glModel = $glModel;

		$this->glObjectId = $glObjectId;
		$this->glAppId = $glAppId;
		$this->glAppUserId = $glAppUserId;
		$this->glOldAttr = $glOldAttr;
		$this->glNewAttr = $glNewAttr;
		$this->glFormattedAttr = $glFormattedAttr;
	}
}