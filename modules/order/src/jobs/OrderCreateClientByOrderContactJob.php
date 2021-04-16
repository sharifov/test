<?php

namespace modules\order\src\jobs;

use modules\order\src\entities\orderContact\OrderContact;
use modules\order\src\entities\orderContact\OrderContactRepository;
use sales\helpers\app\AppHelper;
use sales\repositories\NotFoundException;
use sales\services\client\ClientManageService;
use yii\queue\JobInterface;

/**
 * Class OrderCreateClientByOrderContact
 * @package modules\order\src\jobs
 *
 * @property int $orderContactId
 * @property int $projectId
 */
class OrderCreateClientByOrderContactJob implements JobInterface
{
    public int $orderContactId;
    public int $projectId;

    public function __construct(int $orderContactId, int $projectId)
    {
        $this->orderContactId = $orderContactId;
        $this->projectId = $projectId;
    }

    public function execute($queue)
    {
        try {
            $orderContact = OrderContact::findOne(['oc_id' => $this->orderContactId]);

            if (!$orderContact) {
                throw new NotFoundException('Order Contact not found by id: ' . $this->orderContactId);
            }

            $clientManageService = \Yii::createObject(ClientManageService::class);
            $client = $clientManageService->createBasedOnOrderContact($orderContact, $this->projectId);

            $orderContact->oc_client_id = $client->id;
            $repo = \Yii::createObject(OrderContactRepository::class);
            $repo->save($orderContact);
        } catch (NotFoundException $throwable) {
            AppHelper::throwableLogger(
                $throwable,
                'OrderCreateClientByOrderContact:Execute:NotFoundException'
            );
        } catch (\Throwable $throwable) {
            AppHelper::throwableLogger(
                $throwable,
                'OrderCreateClientByOrderContact:Execute:Throwable'
            );
            throw new \Exception($throwable->getMessage());
        }
    }
}
