<?php

namespace modules\user\userFeedback\service;

use common\models\Notifications;
use modules\user\userFeedback\entity\UserFeedback;
use modules\user\userFeedback\entity\UserFeedbackData;
use modules\user\userFeedback\forms\UserFeedbackBugForm;
use modules\user\userFeedback\UserFeedbackRepository;

/**
 * Class UserFeedbackService
 *
 */
class UserFeedbackService
{
    private const NOTIFICATION_MESSAGE_ON_CREATE = [
        'n_title' => 'Thanks for your feedback!',
        'n_message' => 'Feedback #%s "%s" is under consideration',
        'n_type_id' => Notifications::TYPE_SUCCESS,
    ];
    private const NOTIFICATION_MESSAGE_FROM_NEW_TO_PENDING = [
        'n_title' => 'Alert',
        'n_message' => 'Your Feedback #%s "%s" has been sent for processing',
        'n_type_id' => Notifications::TYPE_INFO,
    ];
    private const NOTIFICATION_MESSAGE_CLOSED = [
        'n_title' => 'Thanks!',
        'n_message' => 'Your Feedback #%s "%s" has been successfully closed',
        'n_type_id' => Notifications::TYPE_SUCCESS,
    ];
    private const NOTIFICATION_MESSAGE_DONE = [
        'n_title' => 'Thanks!',
        'n_message' => 'Your Feedback #%s  "%s" has been successfully completed',
        'n_type_id' => Notifications::TYPE_SUCCESS,
    ];

    private $repository;

    public function __construct(UserFeedbackRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @param UserFeedbackBugForm $form
     * @param UserFeedbackData $dto
     * @return int
     * @throws \Throwable
     */
    public function create(UserFeedbackBugForm $form, UserFeedbackData $dto): int
    {
        $model = UserFeedback::createNewFeedback($form->title, $form->message, $form->type_id, $dto->toArray());
        $this->repository->save($model);
        $this->sendNotification($model->uf_id, $model->uf_created_user_id, $model->uf_title, self::NOTIFICATION_MESSAGE_ON_CREATE);
        return $model->uf_id;
    }

    /**
     * @param UserFeedback $model
     * @param array $data
     * @return void
     * @throws \Throwable
     */
    public function update(UserFeedback $model, array $data)
    {
        $oldStatus = $model->uf_status_id;
        $model->load($data);
        $newStatus = $model->uf_status_id;
        $model->uf_data_json = @json_decode($model->uf_data_json);
        $this->repository->save($model);
        $notificationMessage = $this->getNotificationMessage($oldStatus, $newStatus);
        if (!empty($notificationMessage)) {
            $this->sendNotification($model->uf_id, $model->uf_created_user_id, $model->uf_title, $notificationMessage);
        }
    }

    public function resolve(UserFeedback $model, string $resolution, int $newStatus, int $userId)
    {
        $oldStatus = $model->uf_status_id;
        $model->uf_resolution         = $resolution;
        $model->uf_resolution_user_id = $userId;
        $model->uf_resolution_dt      = date('Y-m-d H:i:s');
        $model->uf_status_id = $newStatus;
        $this->repository->save($model);
        $notificationMessage = $this->getNotificationMessage($oldStatus, $newStatus);
        if (!empty($notificationMessage)) {
            $this->sendNotification($model->uf_id, $model->uf_created_user_id, $model->uf_title, $notificationMessage);
        }
    }

    /**
     * @param UserFeedback $model
     * @param int|null $statusId
     * @param int|null $typeId
     * @return void
     * @throws \Throwable
     */
    public function updateStatusAndTypeId(UserFeedback $model, ?int $statusId, ?int $typeId)
    {
        $oldStatus = $model->uf_status_id;
        if (isset($statusId)) {
            $model->uf_status_id = $statusId;
        }
        if (isset($typeId)) {
            $model->uf_type_id = $typeId;
        }
        $newStatus = $model->uf_status_id;
        $this->repository->save($model);
        $notificationMessage = $this->getNotificationMessage($oldStatus, $newStatus);
        if (!empty($notificationMessage)) {
            $this->sendNotification($model->uf_id, $model->uf_created_user_id, $model->uf_title, $notificationMessage);
        }
    }

    /**
     * @param int $oldStatus
     * @param int $newStatus
     * @return array|null
     */
    private function getNotificationMessage(int $oldStatus, int $newStatus): ?array
    {
        $status = null;
        if ($oldStatus === $newStatus) {
            return $status;
        }
        if ($oldStatus === UserFeedback::STATUS_NEW && $newStatus === UserFeedback::STATUS_PENDING) {
            $status = self::NOTIFICATION_MESSAGE_FROM_NEW_TO_PENDING;
        } elseif ($newStatus === UserFeedback::STATUS_CANCEL) {
            $status = self::NOTIFICATION_MESSAGE_CLOSED;
        } elseif ($newStatus === UserFeedback::STATUS_DONE) {
            $status = self::NOTIFICATION_MESSAGE_DONE;
        }
        return $status;
    }

    /**
     * @param int $uf_id
     * @param int $user_id
     * @param string $title
     * @param array $message
     * @return void
     */
    private function sendNotification(int $uf_id, int $user_id, string $title, array $message): void
    {
        $subject = $message['n_title'] ?? '';
        $body    = sprintf($message['n_message'] ?? '', $uf_id, $title);
        $type    = $message['n_type_id'] ?? '';
        Notifications::createAndPublish(
            $user_id,
            $subject,
            $body,
            $type,
            true
        );
    }
}
