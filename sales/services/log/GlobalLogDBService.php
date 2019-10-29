<?php

namespace sales\services\log;

use sales\entities\log\GlobalLog;
use sales\repositories\log\GlobalLogRepository;
use sales\logger\db\GlobalLogInterface;
use sales\logger\db\LogDTO;

/**
 * Class GlobalLogDBService
 * @package sales\services\log
 *
 * @property GlobalLogRepository $globalLogRepository
 */
class GlobalLogDBService implements GlobalLogInterface
{
	/**
	 * @var GlobalLogRepository
	 */
	private $globalLogRepository;

	/**
	 * GlobalLogsService constructor.
	 * @param GlobalLogRepository $globalLogRepository
	 */
	public function __construct(GlobalLogRepository $globalLogRepository)
	{
		$this->globalLogRepository = $globalLogRepository;
	}

	/**
	 * @param LogDTO $data
	 */
	public function log(LogDTO $data): void
	{
		$log = GlobalLog::create(
			$data->glModel,
			$data->glObjectId,
			$data->glAppId,
			$data->glAppUserId,
			$data->glOldAttr,
			$data->glNewAttr,
			$data->glFormattedAttr
		);
		$this->globalLogRepository->save($log);
	}
}