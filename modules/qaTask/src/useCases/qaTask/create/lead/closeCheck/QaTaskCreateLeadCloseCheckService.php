<?php

namespace modules\qaTask\src\useCases\qaTask\create\lead\closeCheck;

use common\models\Lead;
use modules\qaTask\src\entities\qaTask\QaTask;
use modules\qaTask\src\entities\qaTask\QaTaskCreateType;
use modules\qaTask\src\entities\qaTask\QaTaskObjectType;
use modules\qaTask\src\entities\qaTask\QaTaskRepository;
use modules\qaTask\src\entities\qaTaskCategory\QaTaskCategoryQuery;
use modules\qaTask\src\entities\qaTaskStatusLog\CreateDto;
use modules\qaTask\src\useCases\qaTask\QaTaskActions;
use src\dispatchers\EventDispatcher;
use src\helpers\app\AppHelper;

/**
 * Class QaTaskCreateLeadCloseCheckService
 * @package modules\qaTask\src\useCases\qaTask\create\lead\closeCheck
 *
 * @property-read QaTaskRepository $qaTaskRepository
 * @property-read EventDispatcher $eventDispatcher
 */
class QaTaskCreateLeadCloseCheckService
{
    public const CATEGORY_KEY = 'qa_lead_close_check';
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

    public function handle(Rule $rule, Lead $lead): void
    {
        try {
            $categoryId = $this->getCategoryIdByKey($rule->qaTaskCategoryKey);
            $this->createTask($lead, $categoryId);
        } catch (\Throwable $e) {
            \Yii::error(AppHelper::throwableLog($e, true), 'QaTaskCreateLeadCloseCheckService::handle::Throwable');
        }
    }

    private function createTask(Lead $lead, int $categoryId): int
    {
        $task = QaTask::create(
            QaTaskObjectType::LEAD,
            $lead->id,
            $lead->project_id,
            $lead->l_dep_id,
            $categoryId,
            QaTaskCreateType::TRIGGER,
            null
        );
        $task->detachBehavior('user');
        $this->qaTaskRepository->save($task);

        $this->eventDispatcher->dispatch(new QaTaskCreateLeadCloseCheckEvent(
            $task,
            new CreateDto(
                $task,
                null,
                $task->t_status_id,
                null,
                null,
                QaTaskActions::CREATE_LEAD_CLOSE_CHECK,
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
}
