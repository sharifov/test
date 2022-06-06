<?php

namespace src\repositories;

use src\helpers\app\DBHelper;

/**
 * Class AbstractRepositoryYearMonthPartition
 */
abstract class AbstractRepositoryYearMonthPartition extends AbstractRepositoryWithEvent
{
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
                        [
                            'message' => 'Partition created',
                            'table' => $this->getModel()::tableName(),
                            'partitionCommand' => $partitionCommand,
                        ],
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
