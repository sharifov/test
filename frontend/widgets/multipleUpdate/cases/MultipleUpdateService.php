<?php

namespace frontend\widgets\multipleUpdate\cases;

use common\components\purifier\Purifier;
use common\models\Notifications;
use frontend\widgets\notification\NotificationMessage;
use sales\entities\cases\Cases;
use sales\entities\cases\CasesStatus;
use sales\services\cases\CasesManageService;
use Yii;
use yii\helpers\Html;

/**
 * Class MultipleUpdateService
 *
 * @property CasesManageService $service
 * @property array $messages
 */
class MultipleUpdateService
{
    private $service;
    private $messages = [];

    public function __construct(CasesManageService $service)
    {
        $this->service = $service;
    }

    public function update(MultipleUpdateForm $form): array
    {
        foreach ($form->ids as $id) {
            if (!$case = Cases::findOne($id)) {
                $this->addErrorMessage('ID ' . $id . ' not found');
                continue;
            }
            if ($form->isChangeStatus()) {
                $this->changeStatus($form, $case);
                continue;
            }
            if ($form->isProcessing()) {
                $this->processing($form, $case);
                continue;
            }
        }
        return $this->messages;
    }

    private function processing(MultipleUpdateForm $form, Cases $case): void
    {
        try {
            $this->service->processing($case, $form->userId, $form->getCreatorId());
            $this->addSuccessMessage($this->movedProcessingMessage($case));

            if ($form->userId !== $form->getCreatorId()) {
                $creator = $form->getCreator();
                $title = 'Title: New Case Assigned';
                $linkToCase = Purifier::createCaseShortLink($case);
                $message = 'Message: Case (' . $linkToCase . ') has been assigned to you by user ' . Html::encode($creator->username);

                if ($ntf = Notifications::create($form->userId, $title, $message, Notifications::TYPE_WARNING, true)) {
                    $dataNotification = (Yii::$app->params['settings']['notification_web_socket']) ? NotificationMessage::add($ntf) : [];
                    Notifications::publish('getNewNotification', ['user_id' => $form->userId], $dataNotification);
                }
            }

        } catch (\DomainException $e) {
            $this->addErrorMessage('ID ' . $case->cs_id . ' ' . $e->getMessage());
        }
    }

    private function changeStatus(MultipleUpdateForm $form, Cases $case): void
    {
        try {
            if ($form->isPending()) {
                $this->service->pending($case, $form->getCreatorId(), $form->message);
                $this->addSuccessMessage($this->movedStatusMessage($case));
                return;
            }
            if ($form->isFollowUp()) {
                $this->service->followUp($case, $form->getCreatorId(), $form->message, $form->getConvertedDeadline());
                $this->addSuccessMessage($this->movedStatusMessage($case));
                return;
            }
            if ($form->isSolved()) {
                $this->service->solved($case, $form->getCreatorId(), $form->message);
                $this->addSuccessMessage($this->movedStatusMessage($case));
                return;
            }
            if ($form->isTrash()) {
                $this->service->trash($case, $form->getCreatorId(), $form->message);
                $this->addSuccessMessage($this->movedStatusMessage($case));
                return;
            }
        } catch (\DomainException $e) {
            $this->addErrorMessage('ID ' . $case->cs_id . ' ' . $e->getMessage());
        }
    }

    private function movedStatusMessage(Cases $case): string
    {
        return 'ID ' . $case->cs_id .  ' moved to ' . CasesStatus::getName($case->cs_status);
    }

    private function movedProcessingMessage(Cases $case): string
    {
        return 'ID ' . $case->cs_id .  ' moved to ' . CasesStatus::getName($case->cs_status) . ' with owner: ' . $case->owner->username;
    }

    public function formatMessages(Message ...$messages): string
    {
        if (!$messages) {
            return '';
        }

        $out = '<ul>';
        foreach ($messages as $message) {
            $out .= '<li>' . $message->format() . '</li>';
        }
        return $out . '</ul>';
    }

    private function addMessage(Message $message): void
    {
        $this->messages[] = $message;
    }

    private function addSuccessMessage(string $message): void
    {
        $this->addMessage(new SuccessMessage($message));
    }

    private function addErrorMessage(string $message): void
    {
        $this->addMessage(new ErrorMessage($message));
    }
}
