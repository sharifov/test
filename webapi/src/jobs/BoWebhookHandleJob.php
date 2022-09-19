<?php

namespace webapi\src\jobs;

use common\components\jobs\BaseJob;
use src\helpers\app\AppHelper;
use webapi\src\request\BoWebhook;
use yii\base\Model;
use yii\queue\JobInterface;

/**
 * Class BoWebhookHandleJob
 * @package webapi\src\jobss
 *
 * @property Model|null $form
 * @property int|null $requestTypeId
 */
class BoWebhookHandleJob extends BaseJob implements JobInterface
{
    public ?Model $form = null;

    public ?int $requestTypeId = null;

    /**
     * @inheritDoc
     */
    public function execute($queue)
    {
        $this->waitingTimeRegister();
        $this->timeExecution = microtime(true);

        try {
            $service = BoWebhook::getServiceByType($this->requestTypeId);
            if (!$service) {
                throw new \RuntimeException('Service not found by type: ' . BoWebhook::getTypeNameById($this->requestTypeId));
            }
            $service->processRequest($this->form);
        } catch (\Throwable $e) {
            \Yii::error(AppHelper::throwableLog($e, true), 'BoWebhookHandleJob::execute');
        }

        $this->execTimeRegister();
    }
}
