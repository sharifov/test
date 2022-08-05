<?php

namespace src\model\clientChatRequest\useCase\api\create;

use common\components\purifier\Purifier;
use common\models\Notifications;
use frontend\widgets\notification\NotificationMessage;
use src\helpers\ErrorsToStringHelper;
use src\model\clientChat\entity\ClientChat;
use src\model\clientChat\useCase\create\ClientChatRepository;
use src\model\clientChatFeedback\ClientChatFeedbackRepository;
use src\model\clientChatForm\ClientChatFormRepository;
use src\model\clientChatFormResponse\ClientChatFormResponseRepository;
use src\model\clientChatFormResponse\entity\ClientChatFormResponse;

/**
 * Class ClientChatRequestService
 * @package src\model\clientChatRequest\useCase\api\create
 *
 * @property ClientChatRepository $clientChatRepository
 * @property ClientChatFeedbackRepository $clientChatFeedbackRepository
 */
class ClientChatRequestService
{
    /**
     * @var ClientChatRepository
     */
    private ClientChatRepository $clientChatRepository;

    private ClientChatFeedbackRepository $clientChatFeedbackRepository;

    private ClientChatFormResponseRepository $clientChatFormResponseRepository;

    private ClientChatFormRepository $clientChatFormRepository;

    /**
     * ClientChatRequestService constructor.
     * @param ClientChatRepository $clientChatRepository
     * @param ClientChatFeedbackRepository $clientChatFeedbackRepository
     * @param ClientChatFormResponseRepository $clientChatFormResponseRepository
     */
    public function __construct(
        ClientChatRepository $clientChatRepository,
        ClientChatFeedbackRepository $clientChatFeedbackRepository,
        ClientChatFormResponseRepository $clientChatFormResponseRepository,
        ClientChatFormRepository $clientChatFormRepository
    ) {
        $this->clientChatRepository = $clientChatRepository;
        $this->clientChatFeedbackRepository = $clientChatFeedbackRepository;
        $this->clientChatFormResponseRepository = $clientChatFormResponseRepository;
        $this->clientChatFormRepository = $clientChatFormRepository;
    }

    /**
     * @param FeedbackFormBase $feedbackForm
     * @return FeedbackSubmittedForm | FeedbackRejectedForm | FeedbackRequestedForm
     */
    public function createOrUpdateFeedback(FeedbackFormBase $feedbackForm): FeedbackFormBase
    {
        // Getting client chat by room id
        $clientChat = $this->clientChatRepository->getLastByRid($feedbackForm->rid);

        if (is_null($clientChat)) {
            throw new \RuntimeException("client chat with room id `{$feedbackForm->rid}` not found");
        }

        if (!$this->clientChatFeedbackRepository->save($feedbackForm, $clientChat)) {
            throw new \RuntimeException(ErrorsToStringHelper::extractFromModel($feedbackForm), -1);
        }

        self::sendFeedbackNotifications($clientChat);

        return $feedbackForm;
    }

    private static function sendFeedbackNotifications(ClientChat $clientChat): void
    {
        $clientChatLink = Purifier::createChatShortLink($clientChat);
        if (
            $notification = Notifications::create(
                $clientChat->cch_owner_user_id,
                'Feedback received',
                'Feedback received. ' . 'Client Chat; ' . $clientChatLink,
                Notifications::TYPE_INFO,
                true
            )
        ) {
            $dataNotification = (\Yii::$app->params['settings']['notification_web_socket']) ?
                NotificationMessage::add($notification) : [];

            Notifications::publish(
                'getNewNotification',
                ['user_id' => $clientChat->cch_owner_user_id],
                $dataNotification
            );
        }
    }

    public function createOrUpdateFormRequest(ClientChatFormResponseApiForm $form)
    {
        // Getting client chat by room id
        $clientChat = $this->clientChatRepository->getLastByRid($form->rid);

        if (is_null($clientChat)) {
            throw new \RuntimeException("client chat with room id `{$form->rid}` not found");
        }

        $clientChatForm = $this->clientChatFormRepository->getByKey($form->formKey);

        if (is_null($clientChatForm)) {
            throw new \RuntimeException("client chat form with room id `{$form->rid}` not found");
        }

        if (
            $this->clientChatFormResponseRepository->checkDuplicateValue(
                $clientChatForm->ccf_id,
                $clientChat->cch_id,
                $form->formValue
            )
        ) {
            return;
        }

        $model = new ClientChatFormResponse();
        $model->load([
            'ccfr_uid' => $form->rid,
            'ccfr_client_chat_id' => $clientChat->cch_id,
            'ccfr_form_id' => $clientChatForm->ccf_id,
            'ccfr_value' => $form->formValue,
            'ccfr_rc_created_dt' => date('Y-m-d H:i:s', strtotime($form->createdAt)),
            'ccfr_created_dt' => date('Y-m-d H:i:s')
        ], '');

        $this->clientChatFormResponseRepository->save($model);
    }
}
