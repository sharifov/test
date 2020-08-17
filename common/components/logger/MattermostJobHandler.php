<?php

namespace common\components\logger;

class MattermostJobHandler implements \kivork\mattermostLogTarget\Handler
{
    public function handle(array $containerSettings, string $chanelId, string $body): void
    {
        $job = new \kivork\mattermostLogTarget\Job();
        $job->containerSettings = $containerSettings;
        $job->chanelId = $chanelId;
        $job->body = $body;
        \Yii::$app->queue_system_services->push($job);
    }
}
