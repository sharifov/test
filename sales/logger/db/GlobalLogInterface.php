<?php


namespace sales\logger\db;

/**
 * Interface GlobalLogInterface
 * @package sales\services\logger\db
 */
interface GlobalLogInterface
{
	public function log(LogDTO $data);
}