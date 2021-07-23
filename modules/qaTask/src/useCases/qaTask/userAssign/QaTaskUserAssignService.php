<?php

namespace modules\qaTask\src\useCases\qaTask\userAssign;

use modules\qaTask\src\entities\qaTask\QaTaskRepository;
use modules\qaTask\src\services\QaTaskStatusLogService;
use modules\qaTask\src\useCases\qaTask\QaTaskActions;
use modules\qaTask\src\entities\qaTaskStatusLog\CreateDto;

/**
 * Class QaTaskUserAssignService
 * @package modules\qaTask\src\useCases\qaTask\userAssign
 *
 * @property-read QaTaskRepository $qaTaskRepository
 * @property-read QaTaskStatusLogService $qaTaskStatusLogService
 */
class QaTaskUserAssignService
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

    public function handle(UserAssignForm $form, int $creatorId): void
    {
        foreach ($form->gids as $gid) {
            $task = $this->qaTaskRepository->findByGid($gid);

            $task->t_assigned_user_id = $form->userId;

            $this->qaTaskRepository->save($task);

            $this->qaTaskStatusLogService->log(new CreateDto(
                $task,
                $task->t_status_id,
                $task->t_status_id,
                null,
                $form->comment,
                QaTaskActions::USER_ASSIGN,
                $task->t_assigned_user_id,
                $creatorId
            ));
        }
    }
}
