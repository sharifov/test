<?php

namespace modules\qaTask\src\listeners;

use sales\helpers\app\AppHelper;
use Yii;
use modules\qaTask\src\entities\qaTask\QaTaskChangeStateInterface;
use modules\qaTask\src\services\QaTaskStatusLogService;

/**
 * Class QaTaskChangeStateEventListener
 *
 * @property QaTaskStatusLogService $service
 */
class QaTaskChangeStateEventListener
{
    private $service;

    public function __construct(QaTaskStatusLogService $service)
    {
        $this->service = $service;
    }

    public function handle(QaTaskChangeStateInterface $event): void
    {
        try {
            $this->service->log($event->getChangeStateLog());
        } catch (\Throwable $e) {
            Yii::error(AppHelper::throwableFormatter($e), 'Listeners:' . self::class);
        }
    }
}
