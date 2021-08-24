<?php

namespace modules\qaTask\src\useCases\qaTask\create\chat\withoutNewMessages;

use modules\qaTask\src\entities\qaTask\QaTask;
use modules\qaTask\src\entities\qaTask\QaTaskCreateType;
use modules\qaTask\src\entities\qaTask\QaTaskObjectType;
use modules\qaTask\src\entities\qaTask\QaTaskRepository;
use modules\qaTask\src\entities\qaTaskCategory\QaTaskCategoryQuery;
use modules\qaTask\src\entities\qaTaskStatusLog\CreateDto;
use modules\qaTask\src\useCases\qaTask\QaTaskActions;
use sales\dispatchers\EventDispatcher;
use sales\model\clientChat\entity\ClientChat;

/**
 * Class QaTaskCreateChatWithoutNewMessagesService
 * @package modules\qaTask\src\useCases\qaTask\create\chat\withoutNewMessages
 *
 * @property QaTaskRepository $qaTaskRepository
 * @property EventDispatcher $eventDispatcher
 */
class QaTaskCreateChatWithoutNewMessagesService
{
    public const CATEGORY_KEY = 'qa_chat_without_new_messages';
    /**
     * @var QaTaskRepository
     */
    private QaTaskRepository $qaTaskRepository;
    /**
     * @var EventDispatcher
     */
    private EventDispatcher $eventDispatcher;

    public function __construct(QaTaskRepository $qaTaskRepository, EventDispatcher $eventDispatcher)
    {
        $this->qaTaskRepository = $qaTaskRepository;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @param Rule $rule
     * @param ClientChat $chat
     */
    public function handle(Rule $rule, ClientChat $chat): void
    {
        $categoryId = $this->getCategoryIdByKey($rule->qa_task_category_key);

        $task = QaTask::create(
            QaTaskObjectType::CHAT,
            $chat->cch_id,
            $chat->cch_project_id,
            $chat->cchChannel->ccc_dep_id,
            $categoryId,
            QaTaskCreateType::JOB,
            null
        );
        $task->detachBehavior('user');
        $this->qaTaskRepository->save($task);

        $this->eventDispatcher->dispatch(new QaTaskCreateChatWithoutNewMessagesEvent(
            $task,
            new CreateDto(
                $task,
                null,
                $task->t_status_id,
                null,
                null,
                QaTaskActions::CREATE_LEAD_TRASH_CHECK,
                $task->t_assigned_user_id,
                null
            )
        ));
    }

    private function getCategoryIdByKey(string $key): int
    {
        if ($categoryId = QaTaskCategoryQuery::getCategoryIdByKey($key)) {
            return $categoryId;
        }
        throw new \DomainException('Not found category with key: ' . $key);
    }
}
