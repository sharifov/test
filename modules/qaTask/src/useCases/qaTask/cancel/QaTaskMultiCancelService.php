<?php

namespace modules\qaTask\src\useCases\qaTask\cancel;

use modules\qaTask\src\entities\qaTask\QaTaskRepository;
use modules\qaTask\src\services\QaTaskStatusLogService;
use modules\qaTask\src\useCases\qaTask\QaTaskActions;
use modules\qaTask\src\entities\qaTaskStatusLog\CreateDto;

class QaTaskMultiCancelService
{
    /**
     * @var QaTaskRepository
     */
    private QaTaskRepository $qaTaskRepository;
    /**
     * @var QaTaskStatusLogService
     */
    private QaTaskStatusLogService $qaTaskStatusLogService;

    public function __construct(QaTaskRepository $qaTaskRepository, QaTaskStatusLogService $qaTaskStatusLogService)
    {
        $this->qaTaskRepository = $qaTaskRepository;
        $this->qaTaskStatusLogService = $qaTaskStatusLogService;
    }

    public function handle(QaTaskMultipleCancelFrom $form, int $creatorId): void
    {
        foreach ($form->gids as $gid) {
            $task = $this->qaTaskRepository->findByGid($gid);
            $prevStatus = $task->t_status_id;
            $task->t_assigned_user_id = $form->userId;
            $task->t_status_id = $form->status;

            $this->qaTaskRepository->save($task);

            $this->qaTaskStatusLogService->log(new CreateDto(
                $task,
                $prevStatus,
                $task->t_status_id,
                $form->actionId,
                $form->comment,
                QaTaskActions::CANCEL,
                $task->t_assigned_user_id,
                $creatorId
            ));
        }
    }
}
