<?php

namespace modules\qaTask\src\useCases\qaTask;

use modules\qaTask\src\entities\qaTask\QaTaskRepository;
use src\access\ProjectAccessService;
use src\dispatchers\EventDispatcher;
use src\repositories\user\UserRepository;
use src\services\TransactionManager;
use yii\rbac\CheckAccessInterface;

/**
 * Class QaTaskActionsService
 *
 * @property QaTaskRepository $taskRepository
 * @property UserRepository $userRepository
 * @property EventDispatcher $eventDispatcher
 * @property ProjectAccessService $projectAccessService
 * @property CheckAccessInterface $accessChecker
 * @property TransactionManager $transactionManager
 */
class QaTaskActionsService
{
    protected $taskRepository;
    protected $userRepository;
    protected $eventDispatcher;
    protected $projectAccessService;
    protected $accessChecker;
    protected $transactionManager;

    public function __construct(
        QaTaskRepository $taskRepository,
        UserRepository $userRepository,
        EventDispatcher $eventDispatcher,
        ProjectAccessService $projectAccessService,
        CheckAccessInterface $accessChecker,
        TransactionManager $transactionManager
    ) {
        $this->taskRepository = $taskRepository;
        $this->userRepository = $userRepository;
        $this->eventDispatcher = $eventDispatcher;
        $this->projectAccessService = $projectAccessService;
        $this->accessChecker = $accessChecker;
        $this->transactionManager = $transactionManager;
    }
}
