<?php


namespace sales\repositories\log;

use sales\entities\log\GlobalLog;

/**
 * Class GlobalLogsRepository
 * @package sales\repositories\logs
 */
class GlobalLogRepository
{
	/**
	 * @param GlobalLog $globalLog
	 * @return int
	 */
	public function save(GlobalLog $globalLog): int
	{
		if (!$globalLog->save(false)) {
			throw new \RuntimeException('Saving error');
		}
		return $globalLog->gl_id;
	}
}