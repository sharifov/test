<?php

namespace modules\taskList\src\entities\userTask\repository;

use modules\taskList\src\entities\userTask\UserTask;
use src\helpers\app\DBHelper;
use src\repositories\AbstractBaseRepository;
use src\repositories\AbstractRepositoryWithEvent;

/**
 * Class UserTaskRepository
 *
 * @property UserTask $model
 */
class UserTaskRepository extends AbstractRepositoryWithEvent
{
    /**
     * @param UserTask $model
     */
    public function __construct(UserTask $model)
    {
        parent::__construct($model);
    }

    public function getModel(): UserTask
    {
        return $this->model;
    }

    /**
     * @throws \yii\db\Exception
     * @throws \Throwable
     */
    public function save(bool $runValidation = false, string $glue = ' ', int $attempts = 0): AbstractBaseRepository
    {
        try {
            parent::save($runValidation, $glue);
        } catch (\Throwable $e) {
            if (strpos($e->getMessage(), 'Table has no partition')) {
                if ($attempts > 0) {
                    throw new \RuntimeException('Unable to create UserTask partition. ' . $e->getMessage());
                }
                try {
                    $partitionCommand = DBHelper::generateAddPartitionYear($this->getModel()::tableName(), (new \DateTimeImmutable()));
                    \Yii::$app->db->createCommand($partitionCommand)->execute();
                    \Yii::info(
                         ['message' => 'Partition created', 'table' => $this->getModel()::tableName(), 'partitionCommand' => $partitionCommand],
                         'info\UserTaskRepository:Partition:Created'
                    );
                } catch (\Throwable $throwable) {
                    throw $e;
                }

                $this->save($runValidation, $glue, ++$attempts);
            } else {
                throw $e;
            }
        }
        return $this;
    }
}
