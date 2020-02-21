<?php

namespace modules\qaTask\src\useCases\qaTask\create\lead\processingQuality;

use common\models\Lead;
use modules\qaTask\src\entities\qaTask\QaTask;
use modules\qaTask\src\entities\qaTask\QaTaskCreateType;
use modules\qaTask\src\entities\qaTask\QaTaskObjectType;
use modules\qaTask\src\entities\qaTask\QaTaskRepository;
use modules\qaTask\src\entities\qaTaskCategory\QaTaskCategoryQuery;
use modules\qaTask\src\entities\qaTaskStatusLog\CreateDto;
use modules\qaTask\src\useCases\qaTask\QaTaskActions;
use sales\dispatchers\EventDispatcher;

/**
 * Class QaTaskCreateLeadProcessingQualityService
 *
 * @property QaTaskRepository $qaTaskRepository
 * @property EventDispatcher $eventDispatcher
 */
class QaTaskCreateLeadProcessingQualityService
{
    private const CATEGORY_KEY = 'lead_processing_quality';

    private $qaTaskRepository;
    private $eventDispatcher;

    public function __construct(QaTaskRepository $qaTaskRepository, EventDispatcher $eventDispatcher)
    {
        $this->qaTaskRepository = $qaTaskRepository;
        $this->eventDispatcher = $eventDispatcher;
    }

    public function handle(Rule $rule): Log
    {
        $categoryId = $this->getCategoryId();

        $log = new Log();

        foreach ($this->getLeads($rule, $categoryId) as $lead) {
            try {
                $taskId = $this->createTask($lead, $categoryId);
                $log->add(Message::createValid($lead->id, $taskId));
            } catch (\Throwable $e) {
                $log->add(Message::createInvalid($lead->id));
                \Yii::error('Lead Id: ' . $lead->id . PHP_EOL . $e, 'QaTaskCreateLeadProcessingQualityService:createTask');
            }
        }

        return $log;
    }

    /**
     * @param Rule $rule
     * @param int $categoryId
     * @return Lead[]
     */
    private function getLeads(Rule $rule, int $categoryId): array
    {
        $leadIds = QaTaskCreateLeadProcessingQualityQuery::getLeads($rule, $categoryId);

        return Lead::find()->andWhere(['id' => $leadIds])->all();
    }

    private function createTask(Lead $lead, int $categoryId): int
    {
        $task = QaTask::create(
            QaTaskObjectType::LEAD,
            $lead->id,
            $lead->project_id,
            $lead->l_dep_id,
            $categoryId,
            QaTaskCreateType::JOB,
            null
        );

        $this->qaTaskRepository->save($task);

        $this->eventDispatcher->dispatch(new QaTaskCreateLeadProcessingQualityEvent(
            $task,
            new CreateDto(
                $task,
                null,
                $task->t_status_id,
                null,
                null,
                QaTaskActions::CREATE_LEAD_PROCESSING_QUALITY,
                $task->t_assigned_user_id,
                null
            )
        ));

        return $task->t_id;
    }

    private function getCategoryId(): int
    {
        if ($categoryId = QaTaskCategoryQuery::getCategoryIdByKey(self::CATEGORY_KEY)) {
            return $categoryId;
        }
        throw new \DomainException('Not found category with key: ' . self::CATEGORY_KEY);
    }
}
