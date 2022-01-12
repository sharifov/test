<?php

namespace modules\qaTask\src\useCases\qaTask\create\chat\withoutNewMessages;

use modules\qaTask\src\entities\qaTask\QaTask;
use modules\qaTask\src\entities\qaTask\QaTaskCreateType;
use modules\qaTask\src\entities\qaTask\QaTaskObjectType;
use modules\qaTask\src\entities\qaTask\QaTaskRepository;
use modules\qaTask\src\entities\qaTaskCategory\QaTaskCategoryQuery;
use modules\qaTask\src\entities\qaTaskStatus\QaTaskStatus;
use modules\qaTask\src\entities\qaTaskStatusLog\CreateDto;
use modules\qaTask\src\useCases\qaTask\QaTaskActions;
use src\dispatchers\EventDispatcher;
use src\helpers\app\AppHelper;
use src\model\clientChat\entity\ClientChat;
use src\model\clientChatChannel\entity\ClientChatChannel;
use src\model\clientChatLastMessage\entity\ClientChatLastMessage;
use yii\db\Expression;
use yii\helpers\VarDumper;

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
     * @return Log
     */
    public function handle(Rule $rule): Log
    {
        $categoryId = $this->getCategoryIdByKey($rule->qa_task_category_key);

        $log = new Log();

        $chats = $this->getChats($rule->hours_passed);

        foreach ($chats as $chat) {
            $chatId = $chat['cch_id'];
            try {
                $taskId = $this->createTask(
                    $chatId,
                    (int)$chat['cch_project_id'],
                    (int)$chat['ccc_dep_id'],
                    $categoryId
                );
                $log->add(Message::createValid($chatId, $taskId));
            } catch (TaskAlreadyExistsException $e) {
                $log->add(Message::createInvalid($chatId));
            } catch (\Throwable $e) {
                $log->add(Message::createInvalid($chatId));
                \Yii::error('Chat Id: ' . $chatId . PHP_EOL . VarDumper::dumpAsString(AppHelper::throwableLog($e, true)), 'QaTaskCreateChatWithoutNewMessagesService:createTask');
            }
        }

        return $log;
    }

    private function createTask(int $chatId, int $projectId, int $departmentId, int $taskCategoryId): int
    {
        $task = QaTask::create(
            QaTaskObjectType::CHAT,
            $chatId,
            $projectId,
            $departmentId,
            $taskCategoryId,
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

        return $task->t_id;
    }

    private function getCategoryIdByKey(string $key): int
    {
        if ($categoryId = QaTaskCategoryQuery::getCategoryIdByKey($key)) {
            return $categoryId;
        }
        throw new \DomainException('Not found category with key: ' . $key);
    }

    /**
     * @param int $hoursPassed
     * @return array
     */
    private function getChats(int $hoursPassed): array
    {
        $subQuery = QaTask::find()->select(['t_object_id'])->where([
            't_object_type_id' => QaTaskObjectType::CHAT,
            't_create_type_id' => QaTaskCreateType::JOB,
            't_status_id' => [QaTaskStatus::PENDING, QaTaskStatus::PROCESSING]
        ]);
        return ClientChat::find()
            ->select([
                'cch_id',
                'cch_project_id',
                'ccc_dep_id',
            ])
            ->innerJoin(ClientChatLastMessage::tableName(), 'cclm_cch_id = cch_id')
            ->innerJoin(ClientChatChannel::tableName(), 'ccc_id = cch_channel_id')
            ->where(new Expression('(time_to_sec(timediff(:currentDate, cclm_dt)) / 3600) > :hoursPassed'), [
                'currentDate' => date('Y-m-d H:i:s'),
                'hoursPassed' => $hoursPassed
            ])
            ->andWhere(['NOT IN', 'cch_status_id', [ClientChat::STATUS_CLOSED, ClientChat::STATUS_ARCHIVE, ClientChat::STATUS_HOLD]])
            ->andWhere(['NOT IN', 'cch_id', $subQuery])
            ->asArray()
            ->all();
    }
}
