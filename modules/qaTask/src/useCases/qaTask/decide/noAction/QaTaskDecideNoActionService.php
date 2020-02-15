<?php

namespace modules\qaTask\src\useCases\qaTask\decide\noAction;

use modules\qaTask\src\entities\qaTask\QaTask;
use modules\qaTask\src\useCases\qaTask\decide\QaTaskDecideService;

/**
 * Class QaTaskDecideNoActionService
 *
 * @property QaTaskDecideService $decideService
 */
class QaTaskDecideNoActionService
{
    private $decideService;

    public function __construct(
        QaTaskDecideService $decideService
    )
    {
        $this->decideService = $decideService;
    }

    public function handle(QaTaskDecideNoActionForm $form): void
    {
        $this->decideService->decide($form->getTaskId(), $form->getUserId(), $form->getComment());
    }

    public static function can(QaTask $task, int $userId): bool
    {
        return QaTaskDecideService::can($task, $userId);
    }
}
