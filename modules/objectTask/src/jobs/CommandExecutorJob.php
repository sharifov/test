<?php

namespace modules\objectTask\src\jobs;

use modules\objectTask\src\commands\BaseCommand;
use modules\objectTask\src\entities\ObjectTask;
use modules\objectTask\src\entities\repositories\ObjectTaskRepository;
use modules\objectTask\src\services\ObjectTaskService;
use Yii;
use yii\helpers\VarDumper;

class CommandExecutorJob extends BaseObjectTaskJob implements \yii\queue\JobInterface
{
    public function execute($queue)
    {
        $this->waitingTimeRegister();

        $objectTask = ObjectTask::find()
            ->where([
                'ot_uuid' => $this->getObjectTaskId()
            ])
            ->limit(1)
            ->one();

        try {
            $objectTask->setInProgressStatus();
            (new ObjectTaskRepository($objectTask))->save();

            /** @var BaseCommand $command */
            $command = Yii::createObject(
                ObjectTaskService::COMMAND_CLASS_LIST[$objectTask->ot_command],
                [
                    'objectTask' => $objectTask,
                    'config' => $this->getCommandConfig()
                ]
            );

            $process = $command->process();

            if ($process === true) {
                $objectTask->setDoneStatus();
            } else {
                $objectTask->setCanceledStatus();
            }

            (new ObjectTaskRepository($objectTask))->save();
        } catch (\Exception | \Throwable $exception) {
            Yii::error(VarDumper::dumpAsString([
                'objectTaskUuid' => $objectTask->ot_uuid,
                'exception' => $exception
            ]), 'CommandExecutorJob:process');

            $objectTask->setFailedStatus();
            (new ObjectTaskRepository($objectTask))->save();
        }

        return true;
    }
}
